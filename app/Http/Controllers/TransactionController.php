<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\ReturnTools;
use App\Models\Tool;
use App\Models\User;
use App\Models\ActivityLog;
use App\Services\StockService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class TransactionController extends Controller
{
    // lama peminjaman standar (hari), samakan dengan MyBorrowedController
    private const DUE_DAYS = 5;

    public function __construct(private StockService $stock)
    {
    }

    /* ------------------------------------------------------------------ */
    /*  Halaman                                                           */
    /* ------------------------------------------------------------------ */

    public function index(Request $request)
    {
        $tab = $request->query('tab') === 'return' ? 'return' : 'borrowing';

        $bPer = $this->perPage($request->b_per_page);
        $rPer = $this->perPage($request->r_per_page);

        $borrowings = $this->borrowingQuery($request)
            ->paginate($bPer, ['*'], 'borrow_page')
            ->withQueryString()->appends(['tab' => 'borrowing']);

        $returns = $this->returnQuery($request)
            ->paginate($rPer, ['*'], 'return_page')
            ->withQueryString()->appends(['tab' => 'return']);

        $borrowers = User::role('peminjam')->orderBy('name')->get(['id', 'name']);
        $tools     = Tool::orderBy('tools_name')->get(['id', 'tools_code', 'tools_name', 'available', 'status']);

        // peminjaman Active yang belum punya pengajuan return, untuk modal Add Return
        $borrowMap = Borrowing::with(['borrower', 'tool'])
            ->where('status', 'Active')
            ->whereDoesntHave('pendingReturn')
            ->orderBy('borrow_code')
            ->get()
            ->mapWithKeys(fn ($b) => [$b->id => [
                'label'      => $b->borrow_code . ' | ' . ($b->borrower?->name ?? '-'),
                'borrower'   => $b->borrower?->name ?? '-',
                'tool'       => $b->tool?->tools_name ?? '-',
                'borrowDate' => $b->borrow_date?->format('d/m/Y'),
                'dueDate'    => $b->due_date?->format('d/m/Y'),
                'dueIso'     => $b->due_date?->format('Y-m-d'),
            ]])
            ->all();

        return view('transactions.transaction', [
            'tab'            => $tab,
            'borrowings'     => $borrowings,
            'returns'        => $returns,
            'bPer'           => $bPer,
            'rPer'           => $rPer,
            'borrowers'      => $borrowers,
            'tools'          => $tools,
            'borrowMap'      => $borrowMap,
            'nextBorrowCode' => $this->nextCode(Borrowing::class, 'borrow_code', 'TRX'),
            'nextReturnCode' => $this->nextCode(ReturnTools::class, 'return_code', 'RET'),
            'borrowDate'     => today(),
            'dueDate'        => today()->addDays(self::DUE_DAYS),
        ]);
    }

    /* ------------------------------------------------------------------ */
    /*  Borrowing                                                         */
    /* ------------------------------------------------------------------ */

    public function storeBorrowing(Request $request)
    {
        $data = $request->validateWithBag('addBorrowing', $this->borrowingRules());

        try {
            DB::transaction(function () use ($data) {
                $tool = Tool::lockForUpdate()->findOrFail($data['id_tools']);
                $qty  = (int) $data['borrow_qty'];

                if ($tool->status === 'Maintenance' || (int) $tool->available < $qty) {
                    throw new \DomainException("Failed to add. Only {$tool->available} item(s) available for this tool.");
                }

                $this->stock->lend($tool, $qty);

                $borrowing = Borrowing::create([
                    'borrow_code' => $this->nextCode(Borrowing::class, 'borrow_code', 'TRX'),
                    'id_user'     => $data['id_user'],
                    'id_officer'  => Auth::id(),
                    'id_tools'    => $tool->id,
                    'borrow_qty'  => $qty,
                    'borrow_date' => today(),
                    'due_date'    => today()->addDays(self::DUE_DAYS),
                    'status'      => 'Active',
                    'notes'       => $data['notes'] ?? null,
                ]);

                ActivityLog::record('Create', 'Transaction - Borrow', "Added borrowing {$borrowing->borrow_code} ({$tool->tools_name} x{$qty})");

            });
        } catch (\DomainException $e) {
            return $this->redirectTo('borrowing')->with('error', $e->getMessage());
        }

        return $this->redirectTo('borrowing')->with('success', 'Borrowing successfully added.');
    }

    public function updateBorrowing(Request $request, Borrowing $borrowing)
    {
        $validator = Validator::make($request->all(), $this->borrowingRules());

        if ($validator->fails()) {
            return $this->redirectTo('borrowing')->with('error', $validator->errors()->first());
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($borrowing, $data) {
                $b = Borrowing::lockForUpdate()->findOrFail($borrowing->id);

                if (! in_array($b->status, ['Pending', 'Active'])) {
                    throw new \DomainException('Failed to update. Only pending or active borrowings can be edited.');
                }

                if ($b->pendingReturn()->exists()) {
                    throw new \DomainException('Failed to update. This borrowing has a return request waiting for verification.');
                }

                $newQty  = (int) $data['borrow_qty'];
                $oldTool = Tool::lockForUpdate()->findOrFail($b->id_tools);

                if ($b->status === 'Active') {
                    // kembalikan stok lama, lalu ambil stok baru
                    $this->stock->restoreLend($oldTool, (int) $b->borrow_qty);
                }

                $newTool = (int) $data['id_tools'] === (int) $oldTool->id
                    ? $oldTool
                    : Tool::lockForUpdate()->findOrFail($data['id_tools']);

                $changedTool = $newTool->id !== $oldTool->id;

                if (($changedTool && $newTool->status === 'Maintenance') || (int) $newTool->available < $newQty) {
                    throw new \DomainException("Failed to update. Only {$newTool->available} item(s) available for this tool.");
                }

                if ($b->status === 'Active') {
                    $this->stock->lend($newTool, $newQty);
                }

                $b->update([
                    'id_user'    => $data['id_user'],
                    'id_tools'   => $newTool->id,
                    'borrow_qty' => $newQty,
                    'notes'      => $data['notes'] ?? null,
                ]);
            });
        } catch (\DomainException $e) {
            return $this->redirectTo('borrowing')->with('error', $e->getMessage());
        }

        ActivityLog::record('Update', 'Transaction - Borrow', "Updated borrowing {$borrowing->borrow_code}");

        return $this->redirectTo('borrowing')->with('success', 'Borrowing successfully updated.');
    }

    public function destroyBorrowing(Borrowing $borrowing)
    {
        try {
            DB::transaction(function () use ($borrowing) {
                $b = Borrowing::lockForUpdate()->findOrFail($borrowing->id);

                // peminjaman yang masih berjalan: stok dikembalikan
                if ($b->status === 'Active') {
                    $tool = Tool::lockForUpdate()->find($b->id_tools);

                    if ($tool) {
                        $this->stock->restoreLend($tool, (int) $b->borrow_qty);
                    }
                }

                $b->returns()->delete();
                $b->delete();
            });
        } catch (\Throwable $e) {
            report($e);

            return $this->redirectTo('borrowing')
                ->with('error', 'Failed to delete this data. Please try again.');
        }

        ActivityLog::record('Delete', 'Transaction - Borrow', "Deleted borrowing {$borrowing->borrow_code}");

        return $this->redirectTo('borrowing')->with('success', 'Successfully deleted this data');
    }

    /* ------------------------------------------------------------------ */
    /*  Return                                                            */
    /* ------------------------------------------------------------------ */

    public function storeReturn(Request $request)
    {
        $data = $request->validateWithBag('addReturn', [
            'id_borrowing' => ['required', 'exists:borrowings,id'],
            'return_date'  => ['required', 'date'],
            'condition'    => ['required', Rule::in(['Good', 'Damaged', 'Lost'])],
            'notes'        => ['nullable', 'string', 'max:500'],
        ]);

        try {
            DB::transaction(function () use ($data) {
                $b = Borrowing::lockForUpdate()->findOrFail($data['id_borrowing']);

                if ($b->status !== 'Active') {
                    throw new \DomainException('Failed to add. Only active borrowings can be returned.');
                }

                if ($b->pendingReturn()->exists()) {
                    throw new \DomainException('Failed to add. This borrowing already has a return request waiting for verification.');
                }

                $this->assertReturnDate($data['return_date'], $b);

                $tool = Tool::lockForUpdate()->findOrFail($b->id_tools);

                $this->stock->applyReturn($tool, (int) $b->borrow_qty, $data['condition']);

                $b->update(['status' => 'Completed']);

                $return = ReturnTools::create([
                    'return_code'  => $this->nextCode(ReturnTools::class, 'return_code', 'RET'),
                    'id_borrowing' => $b->id,
                    'id_officer'   => Auth::id(),
                    'return_date'  => $data['return_date'],
                    'condition'    => $data['condition'],
                    'status'       => 'Approved',
                    'notes'        => $data['notes'] ?? null,
                ]);

                ActivityLog::record('Create', 'Transaction - Return', "Added return {$return->return_code} for {$b->borrow_code}");
            });
        } catch (\DomainException $e) {
            return $this->redirectTo('return')->with('error', $e->getMessage());
        }

        return $this->redirectTo('return')->with('success', 'Return successfully added.');
    }

    public function updateReturn(Request $request, ReturnTools $returnTool)
    {
        $validator = Validator::make($request->all(), [
            'return_date' => ['required', 'date'],
            'condition'   => ['required', Rule::in(['Good', 'Damaged', 'Lost'])],
            'notes'       => ['nullable', 'string', 'max:500'],
        ]);

        if ($validator->fails()) {
            return $this->redirectTo('return')->with('error', $validator->errors()->first());
        }

        $data = $validator->validated();

        try {
            DB::transaction(function () use ($returnTool, $data) {
                $r = ReturnTools::lockForUpdate()->findOrFail($returnTool->id);
                $b = Borrowing::lockForUpdate()->findOrFail($r->id_borrowing);

                $this->assertReturnDate($data['return_date'], $b);

                // hanya pengembalian yang sudah disetujui yang berpengaruh ke stok
                if ($r->status === 'Approved') {
                    $tool = Tool::lockForUpdate()->findOrFail($b->id_tools);
                    $qty  = (int) $b->borrow_qty;

                    $this->stock->revertReturn($tool, $qty, $r->condition);
                    $this->stock->applyReturn($tool, $qty, $data['condition']);
                }

                $r->update([
                    'return_date' => $data['return_date'],
                    'condition'   => $data['condition'],
                    'notes'       => $data['notes'] ?? null,
                ]);
            });
        } catch (\DomainException $e) {
            return $this->redirectTo('return')->with('error', $e->getMessage());
        }

        ActivityLog::record('Update', 'Transaction - Return', "Updated return {$returnTool->return_code}");

        return $this->redirectTo('return')->with('success', 'Return successfully updated.');
    }

    public function destroyReturn(ReturnTools $returnTool)
    {
        try {
            DB::transaction(function () use ($returnTool) {
                $r = ReturnTools::lockForUpdate()->findOrFail($returnTool->id);

                // pengembalian yang sudah disetujui dibatalkan: stok ditarik, peminjaman Active lagi
                if ($r->status === 'Approved') {
                    $b = Borrowing::lockForUpdate()->find($r->id_borrowing);

                    if ($b) {
                        $tool = Tool::lockForUpdate()->find($b->id_tools);

                        if ($tool) {
                            $this->stock->revertReturn($tool, (int) $b->borrow_qty, $r->condition);
                        }

                        $b->update(['status' => 'Active']);
                    }
                }

                $r->delete();
            });
        } catch (\Throwable $e) {
            report($e);

            return $this->redirectTo('return')
                ->with('error', 'Failed to delete this data. Please try again.');
        }

        ActivityLog::record('Delete', 'Transaction - Return', "Deleted return {$returnTool->return_code}");

        return $this->redirectTo('return')->with('success', 'Successfully deleted this data');
    }

    /* ------------------------------------------------------------------ */
    /*  Export Excel                                                      */
    /* ------------------------------------------------------------------ */

    public function export(Request $request, string $type)
    {
        if ($type === 'borrowing') {
            $headers = ['No', 'Borrow ID', 'Borrower', 'Tool Code', 'Tool Name', 'Qty', 'Borrow Date', 'Due Date', 'Status', 'Processed By', 'Notes'];

            $rows = $this->borrowingQuery($request)->get()->map(fn ($b, $i) => [
                $i + 1,
                $b->borrow_code,
                $b->borrower?->name ?? '-',
                $b->tool?->tools_code ?? '-',
                $b->tool?->tools_name ?? '-',
                (int) $b->borrow_qty,
                $b->borrow_date?->format('d/m/Y') ?? '',
                $b->due_date?->format('d/m/Y') ?? '',
                $b->display_status,
                $b->officer?->name ?? '-',
                $b->notes ?: '-',
            ])->all();

            $title = 'Borrowing';
        } else {
            $headers = ['No', 'Return ID', 'Borrow ID', 'Borrower', 'Tool Name', 'Qty', 'Borrow Date', 'Due Date', 'Return Date', 'Condition', 'Status', 'Processed By', 'Notes'];

            $rows = $this->returnQuery($request)->get()->map(fn ($r, $i) => [
                $i + 1,
                $r->return_code,
                $r->borrowing?->borrow_code ?? '-',
                $r->borrowing?->borrower?->name ?? '-',
                $r->borrowing?->tool?->tools_name ?? '-',
                (int) ($r->borrowing?->borrow_qty ?? 0),
                $r->borrowing?->borrow_date?->format('d/m/Y') ?? '',
                $r->borrowing?->due_date?->format('d/m/Y') ?? '',
                $r->return_date?->format('d/m/Y') ?? '',
                $r->condition,
                $r->display_status,
                $r->officer?->name ?? '-',
                $r->notes ?: '-',
            ])->all();

            $title = 'Return';
        }

        $spreadsheet = $this->buildSheet($title, $headers, $rows);
        $filename    = 'transactions-' . $type . '-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function buildSheet(string $title, array $headers, array $rows): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        foreach ($headers as $i => $header) {
            $sheet->setCellValueExplicit([$i + 1, 1], $header, DataType::TYPE_STRING);
        }

        foreach ($rows as $r => $row) {
            foreach ($row as $c => $value) {
                $coordinate = [$c + 1, $r + 2];

                // teks selalu ditulis sebagai teks, supaya isi yang diawali "=" tidak dibaca sebagai rumus
                if (is_int($value)) {
                    $sheet->setCellValueExplicit($coordinate, $value, DataType::TYPE_NUMERIC);
                } else {
                    $sheet->setCellValueExplicit($coordinate, (string) $value, DataType::TYPE_STRING);
                }
            }
        }

        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $lastRow = max(count($rows) + 1, 1);

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => 'solid', 'startColor' => ['rgb' => '0F766E']],
            'alignment' => ['horizontal' => 'center'],
        ]);

        $sheet->getStyle("A1:{$lastCol}{$lastRow}")->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'D1D5DB']]],
        ]);

        for ($i = 1; $i <= count($headers); $i++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i))->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        return $spreadsheet;
    }

    /* ------------------------------------------------------------------ */
    /*  Helper                                                            */
    /* ------------------------------------------------------------------ */

    private function borrowingQuery(Request $request)
    {
        return Borrowing::with(['borrower', 'tool.category', 'officer', 'pendingReturn', 'latestReturn'])
            ->when($request->b_search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('borrow_code', 'like', "%{$search}%")
                      ->orWhereHas('borrower', fn ($b) => $b->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('tool', fn ($t) => $t->where('tools_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->b_status, function ($query, $status) {
                $rejected = fn ($r) => $r->where('status', 'Rejected');

                return match ($status) {
                    'Overdue'         => $query->where('status', 'Active')->whereDate('due_date', '<', today())
                                            ->whereDoesntHave('pendingReturn')->whereDoesntHave('returns', $rejected),
                    'Active'          => $query->where('status', 'Active')->whereDate('due_date', '>=', today())
                                            ->whereDoesntHave('pendingReturn')->whereDoesntHave('returns', $rejected),
                    'Pending Return'  => $query->where('status', 'Active')->whereHas('pendingReturn'),
                    'Return Rejected' => $query->where('status', 'Active')->whereDoesntHave('pendingReturn')
                                            ->whereHas('returns', $rejected),
                    default           => $query->where('status', $status),
                };
            })
            ->orderByRaw("status = 'Pending' desc")
            ->orderByDesc('id');
    }

    private function returnQuery(Request $request)
    {
        return ReturnTools::with(['borrowing.borrower', 'borrowing.tool.category', 'officer'])
            ->when($request->r_search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('return_code', 'like', "%{$search}%")
                      ->orWhereHas('borrowing.borrower', fn ($b) => $b->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('borrowing.tool', fn ($t) => $t->where('tools_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->r_condition, fn ($q, $c) => $q->where('condition', $c))
            ->when($request->r_status, fn ($q, $s) => $q->where('status', $s))
            ->orderByRaw("status = 'Pending' desc")
            ->orderByDesc('id');
    }

    private function borrowingRules(): array
    {
        return [
            'id_user' => [
                'required',
                'exists:users,id',
                function ($attribute, $value, $fail) {
                    if (! User::role('peminjam')->whereKey($value)->exists()) {
                        $fail('The selected borrower is not a borrower account.');
                    }
                },
            ],
            'id_tools'   => ['required', 'exists:tools,id'],
            'borrow_qty' => ['required', 'integer', 'min:1'],
            'notes'      => ['nullable', 'string', 'max:500'],
        ];
    }

    private function assertReturnDate(string $returnDate, Borrowing $borrowing): void
    {
        if ($borrowing->borrow_date && Carbon::parse($returnDate)->lt($borrowing->borrow_date)) {
            throw new \DomainException('Return date cannot be earlier than the borrow date.');
        }
    }

    private function perPage($value): int
    {
        return in_array((int) $value, [5, 10, 25]) ? (int) $value : 5;
    }

    private function redirectTo(string $tab)
    {
        return redirect()->route('transactions.transaction', ['tab' => $tab]);
    }

    // kode berikutnya: TRX-04, RET-03, dan seterusnya
    private function nextCode(string $modelClass, string $column, string $prefix): string
    {
        $number = $modelClass::count() + 1;

        do {
            $code   = $prefix . '-' . str_pad($number, 2, '0', STR_PAD_LEFT);
            $exists = $modelClass::where($column, $code)->exists();
            $number++;
        } while ($exists);

        return $code;
    }
}