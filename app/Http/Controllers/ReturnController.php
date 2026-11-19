<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\ReturnTools;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [5, 10, 25]) ? (int) $request->per_page : 5;

        $returns = ReturnTools::with(['borrowing.borrower', 'borrowing.tool.category', 'officer'])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('return_code', 'like', "%{$search}%")
                      ->orWhereHas('borrowing.borrower', fn ($b) => $b->where('name', 'like', "%{$search}%"))
                      ->orWhereHas('borrowing.tool', fn ($t) => $t->where('tools_name', 'like', "%{$search}%"));
                });
            })
            ->orderByRaw("status = 'Pending' desc")   // yang menunggu persetujuan paling atas
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return view('return.return', compact('returns', 'perPage'));
    }

    public function approve(ReturnTools $returnTool)
    {
        try {
            DB::transaction(function () use ($returnTool) {
                // kunci baris supaya dua petugas tidak menyetujui pengajuan yang sama
                $return = ReturnTools::lockForUpdate()->findOrFail($returnTool->id);

                if ($return->status !== 'Pending') {
                    throw new \DomainException('processed');
                }

                $borrowing = Borrowing::lockForUpdate()->findOrFail($return->id_borrowing);
                $tool      = Tool::lockForUpdate()->findOrFail($borrowing->id_tools);
                $qty       = (int) $borrowing->borrow_qty;

                switch ($return->condition) {
                    case 'Lost':
                        // alat hilang: jumlah total berkurang, stok tersedia tidak bertambah
                        $tool->tools_qty = max(0, (int) $tool->tools_qty - $qty);
                        break;

                    case 'Damaged':
                        // alat rusak tetap masuk hitungan, tapi kondisinya ditandai
                        $tool->available       = (int) $tool->available + $qty;
                        $tool->tools_condition = 'Damaged';
                        break;

                    default: // Good
                        $tool->available = (int) $tool->available + $qty;
                }

                // jaga supaya tidak melebihi total
                $tool->available = min((int) $tool->available, (int) $tool->tools_qty);

                if ($tool->status !== 'Maintenance') {
                    $tool->status = $tool->available > 0 ? 'Available' : 'Borrowed';
                }

                $tool->save();

                $borrowing->update(['status' => 'Completed']);

                $return->update([
                    'status'     => 'Approved',
                    'id_officer' => Auth::id(),
                ]);
            });
        } catch (\DomainException $e) {
            return back()->with('error', 'Failed to approve. This return has already been processed.');
        }

        return back()->with('success', 'Successfully approve this data');
    }

    public function reject(ReturnTools $returnTool)
    {
        if ($returnTool->status !== 'Pending') {
            return back()->with('error', 'Failed to reject. This return has already been processed.');
        }

        // peminjaman tetap Active, jadi peminjam bisa mengajukan pengembalian lagi
        $returnTool->update([
            'status'     => 'Rejected',
            'id_officer' => Auth::id(),
        ]);

        return back()->with('success', 'Successfully reject this data');
    }
}