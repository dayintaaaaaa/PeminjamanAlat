<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\ReturnTools;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MyBorrowedController extends Controller
{
    // lama peminjaman standar (hari)
    private const DUE_DAYS = 5;

    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [5, 10, 25]) ? (int) $request->per_page : 5;

        $borrowings = Borrowing::with(['tool.category', 'officer', 'pendingReturn'])
            ->where('id_user', Auth::id())   // hanya milik user yang login
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('borrow_code', 'like', "%{$search}%")
                      ->orWhereHas('tool', fn ($t) => $t->where('tools_name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, function ($query, $status) {
                return match ($status) {
                    'Overdue'        => $query->where('status', 'Active')->whereDate('due_date', '<', today())->whereDoesntHave('pendingReturn'),
                    'Active'         => $query->where('status', 'Active')->whereDate('due_date', '>=', today())->whereDoesntHave('pendingReturn'),
                    'Pending Return' => $query->where('status', 'Active')->whereHas('pendingReturn'),
                    default          => $query->where('status', $status),
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

        Borrowing::create([
            'borrow_code' => $this->nextCode(Borrowing::class, 'borrow_code', 'TRX'),
            'id_user'     => Auth::id(),
            'id_tools'     => $tool->id,
            'borrow_qty'  => $data['qty'],
            'borrow_date' => today(),
            'due_date'    => today()->addDays(self::DUE_DAYS),
            'status'      => 'Pending',
            'notes'       => $data['notes'] ?? null,
        ]);

        return redirect()->route('my-borrowed.my-borrowed')
            ->with('success', 'Borrowing request submitted. Waiting for staff approval.');
    }

    // ajukan pengembalian (muncul di halaman Return petugas sebagai Pending)
    public function requestReturn(Borrowing $borrowing)
    {
        abort_unless((int) $borrowing->id_user === (int) Auth::id(), 403);

        if ($borrowing->status !== 'Active') {
            return back()->with('error', 'Only active borrowings can be returned.');
        }

        if ($borrowing->pendingReturn()->exists()) {
            return back()->with('error', 'A return request for this borrowing is already waiting for staff verification.');
        }

        ReturnTools::create([
            'return_code'      => $this->nextCode(ReturnTools::class, 'return_code', 'RET'),
            'id_borrowing'     => $borrowing->id,
            'return_date'      => today(),
            'return_condition' => 'Good',
            'status'           => 'Pending',
        ]);

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
