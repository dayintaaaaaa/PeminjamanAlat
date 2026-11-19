<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\ReturnTools;
use App\Models\Tool;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MyBorrowedController extends Controller
{
    // lama peminjaman standar (hari)
    private const DUE_DAYS = 5;

    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [5, 10, 25]) ? (int) $request->per_page : 5;

        $borrowings = Borrowing::with(['tool.category', 'officer', 'pendingReturn', 'latestReturn'])
            ->where('id_user', Auth::id())   // hanya milik user yang login
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('borrow_code', 'like', "%{$search}%")
                      ->orWhereHas('tool', fn ($t) => $t->where('tools_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, function ($query, $status) {
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
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        // alat yang masih bisa dipinjam
        $tools = Tool::where('available', '>', 0)
            ->where('status', '!=', 'Maintenance')
            ->orderBy('tools_name')
            ->get(['id', 'tools_code', 'tools_name', 'available']);

        return view('my-borrowed.my-borrowed', [
            'borrowings' => $borrowings,
            'perPage'    => $perPage,
            'tools'      => $tools,
            'nextCode'   => $this->nextCode(Borrowing::class, 'borrow_code', 'TRX'),
            'borrowDate' => today(),
            'dueDate'    => today()->addDays(self::DUE_DAYS),
        ]);
    }

    // ajukan peminjaman baru (status Pending, menunggu persetujuan petugas)
    public function store(Request $request)
    {
        $data = $request->validateWithBag('addRequest', [
            'id_tool' => ['required', 'exists:tools,id'],
            'qty'     => ['required', 'integer', 'min:1'],
            'notes'   => ['nullable', 'string', 'max:500'],
        ]);

        $tool = Tool::findOrFail($data['id_tool']);

        if ($tool->status === 'Maintenance' || $tool->available < $data['qty']) {
            return back()->withInput()->withErrors([
                'qty' => "Only {$tool->available} item(s) available for this tool.",
            ], 'addRequest');
        }

        $borrowing = Borrowing::create([
            'borrow_code' => $this->nextCode(Borrowing::class, 'borrow_code', 'TRX'),
            'id_user'     => Auth::id(),
            'id_tools'    => $tool->id,
            'borrow_qty'  => $data['qty'],
            'borrow_date' => today(),
            'due_date'    => today()->addDays(self::DUE_DAYS),
            'status'      => 'Pending',
            'notes'       => $data['notes'] ?? null,
        ]);

        ActivityLog::record('Create', 'Transaction - Borrow', "Requested borrowing {$borrowing->borrow_code} ({$tool->tools_name} x{$data['qty']})");

        return redirect()->route('my-borrowed.my-borrowed')
            ->with('success', 'Borrowing request submitted. Waiting for staff approval.');
    }

    // ajukan pengembalian (muncul di halaman Return petugas sebagai Pending)
    public function requestReturn(Borrowing $borrowing)
    {
        abort_unless((int) $borrowing->id_user === (int) Auth::id(), 403);

        $error = DB::transaction(function () use ($borrowing) {
            // kunci baris peminjaman supaya klik ganda tidak membuat dua pengajuan
            $b = Borrowing::lockForUpdate()->findOrFail($borrowing->id);

            if ($b->status !== 'Active') {
                return 'Only active borrowings can be returned.';
            }

            if ($b->pendingReturn()->exists()) {
                return 'A return request for this borrowing is already waiting for staff verification.';
            }

            ReturnTools::create([
                'return_code'  => $this->nextCode(ReturnTools::class, 'return_code', 'RET'),
                'id_borrowing' => $b->id,
                'return_date'  => today(),
                'condition'    => 'Good',
                'status'       => 'Pending',
            ]);

            return null;
        });

        if ($error) {
            return back()->with('error', $error);
        }

        ActivityLog::record('Create', 'Transaction - Return', "Requested return for {$borrowing->borrow_code}");

        return back()->with('success', 'Return request submitted. Waiting for staff verification.');
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