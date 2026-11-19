<?php

namespace App\Http\Controllers;

use App\Models\Borrowing;
use App\Models\ReturnTools;
use App\Models\Tool;
use App\Models\ToolCategory;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ---- 4 kartu ----
        $totalTools     = (int) Tool::sum('tools_qty');
        $availableTools = (int) Tool::sum('available');
        $borrowedTools  = (int) Borrowing::where('status', 'Active')->sum('borrow_qty');
        $overdueCount   = Borrowing::where('status', 'Active')->whereDate('due_date', '<', today())->count();
        $categoryCount  = ToolCategory::count();

        // ---- grafik: minggu ini (Senin - Minggu) ----
        $start = now()->startOfWeek();
        $from  = $start->toDateString();
        $to    = $start->copy()->addDays(6)->toDateString();

        $borrowRows = Borrowing::selectRaw('borrow_date as d, SUM(borrow_qty) as total')
            ->whereIn('status', ['Active', 'Completed'])
            ->whereBetween('borrow_date', [$from, $to])
            ->groupBy('borrow_date')
            ->pluck('total', 'd');

        $returnRows = ReturnTools::join('borrowings', 'borrowings.id', '=', 'return_tools.id_borrowing')
            ->where('return_tools.status', 'Approved')
            ->whereBetween('return_tools.return_date', [$from, $to])
            ->selectRaw('return_tools.return_date as d, SUM(borrowings.borrow_qty) as total')
            ->groupBy('return_tools.return_date')
            ->pluck('total', 'd');

        $days = $borrowData = $returnData = [];

        for ($i = 0; $i < 7; $i++) {
            $date         = $start->copy()->addDays($i);
            $key          = $date->toDateString();
            $days[]       = $date->format('D');
            $borrowData[] = (int) ($borrowRows[$key] ?? 0);
            $returnData[] = (int) ($returnRows[$key] ?? 0);
        }

        $maxBar = max(1, max($borrowData), max($returnData));

        // perbandingan dengan minggu lalu
        $prevWeek = (int) Borrowing::whereIn('status', ['Active', 'Completed'])
            ->whereBetween('borrow_date', [
                $start->copy()->subWeek()->toDateString(),
                $start->copy()->subDay()->toDateString(),
            ])
            ->sum('borrow_qty');

        $thisWeek = array_sum($borrowData);
        $trend    = $prevWeek > 0 ? (int) round(($thisWeek - $prevWeek) / $prevWeek * 100) : null;

        // ---- alat paling sering dipinjam ----
        $topTools = Borrowing::select('id_tools', DB::raw('SUM(borrow_qty) as total'))
            ->whereIn('status', ['Active', 'Completed'])
            ->groupBy('id_tools')
            ->orderByDesc('total')
            ->limit(5)
            ->with('tool')
            ->get()
            ->map(fn ($b) => [
                'name'  => $b->tool?->tools_name ?? '-',
                'code'  => $b->tool?->tools_code ?? '-',
                'total' => (int) $b->total,
            ])
            ->all();

        $maxTop = max(1, ...array_column($topTools ?: [['total' => 1]], 'total'));

        return view('dashboard', compact(
            'totalTools', 'availableTools', 'borrowedTools', 'overdueCount', 'categoryCount',
            'days', 'borrowData', 'returnData', 'maxBar', 'trend', 'topTools', 'maxTop'
        ));
    }
}