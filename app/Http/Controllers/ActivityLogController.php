<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $perPage = in_array((int) $request->per_page, [5, 10, 25]) ? (int) $request->per_page : 5;

        $logs = $this->query($request)->paginate($perPage)->withQueryString();

        $actions = ActivityLog::select('action')->distinct()->orderBy('action')->pluck('action');
        $modules = ActivityLog::select('module')->distinct()->orderBy('module')->pluck('module');

        return view('activity-log.activity-log', compact('logs', 'perPage', 'actions', 'modules'));
    }

    public function export(Request $request)
    {
        $logs = $this->query($request)->get();

        $headers = ['No', 'Timestamp', 'Admin/User', 'Action', 'Module', 'Description'];

        $rows = $logs->map(fn ($l, $i) => [
            $i + 1,
            $l->created_at?->format('d/m/Y H:i') ?? '',
            $l->user_name ?? '-',
            $l->action,
            $l->module,
            $l->description,
        ])->all();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Activity Log');

        foreach ($headers as $i => $header) {
            $sheet->setCellValueExplicit([$i + 1, 1], $header, DataType::TYPE_STRING);
        }

        foreach ($rows as $r => $row) {
            foreach ($row as $c => $value) {
                // teks selalu ditulis sebagai teks, supaya isi yang diawali "=" tidak dibaca sebagai rumus
                $sheet->setCellValueExplicit(
                    [$c + 1, $r + 2],
                    is_int($value) ? $value : (string) $value,
                    is_int($value) ? DataType::TYPE_NUMERIC : DataType::TYPE_STRING
                );
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

        return response()->streamDownload(function () use ($spreadsheet) {
            (new Xlsx($spreadsheet))->save('php://output');
        }, 'activity-log-' . now()->format('Y-m-d') . '.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    private function query(Request $request)
    {
        return ActivityLog::query()
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('user_name', 'like', "%{$search}%")
                      ->orWhere('module', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->action, fn ($q, $a) => $q->where('action', $a))
            ->when($request->module, fn ($q, $m) => $q->where('module', $m))
            ->when($request->date_from, fn ($q, $d) => $q->whereDate('created_at', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->whereDate('created_at', '<=', $d))
            ->orderByDesc('id');
    }
}