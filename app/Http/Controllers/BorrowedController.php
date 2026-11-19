<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\Tool;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BorrowedController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [5, 10, 25]) ? (int) $request->per_page : 5;

        $borrowings = Borrowing::with(['borrower', 'tool.category', 'officer', 'pendingReturn', 'latestReturn'])
            ->where('status', '!=', 'Completed')   // yang sudah kembali ada di halaman Return
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('borrow_code', 'like', "%{$search}%")
                      ->orWhereHas('borrower', fn ($b) => $b->where('name', 'like', "%{$search}%"))
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
            ->orderByRaw("status = 'Pending' desc")   // yang menunggu persetujuan paling atas
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('borrowed.borrowed', compact('borrowings', 'perPage'));
    }

    public function approve(Borrowing $borrowing)
    {
        try {
            DB::transaction(function () use ($borrowing) {
                // kunci baris supaya dua petugas tidak menyetujui permintaan yang sama
                $borrowing = Borrowing::lockForUpdate()->findOrFail($borrowing->id);

                if ($borrowing->status !== 'Pending') {
                    throw new \RuntimeException('processed');
                }

                $tool = Tool::lockForUpdate()->findOrFail($borrowing->id_tools);

                if ($tool->available < $borrowing->borrow_qty) {
                    throw new \RuntimeException('stock');
                }

                $tool->available -= $borrowing->borrow_qty;

                if ($tool->available <= 0 && $tool->status !== 'Maintenance') {
                    $tool->status = 'Borrowed';
                }

                $tool->save();

                $borrowing->update([
                    'status'     => 'Active',
                    'id_officer' => Auth::id(),
                ]);
            });
        } catch (\RuntimeException $e) {
            $message = $e->getMessage() === 'stock'
                ? 'Failed to approve. Available stock is lower than the requested quantity.'
                : 'Failed to approve. This request has already been processed.';

            return back()->with('error', $message);
        }

        ActivityLog::record('Approve', 'Transaction - Borrowing', "Approved borrowing {$borrowing->borrow_code}");

        return back()->with('success', 'Successfully approve this data');
    }

    public function reject(Borrowing $borrowing)
    {
        if ($borrowing->status !== 'Pending') {
            return back()->with('error', 'Failed to reject. This request has already been processed.');
        }

        $borrowing->update([
            'status'     => 'Rejected',
            'id_officer' => Auth::id(),
        ]);

        ActivityLog::record('Reject', 'Transaction - Borrowing', "Rejected borrowing {$borrowing->borrow_code}");

        return back()->with('success', 'Successfully reject this data');
    }
}