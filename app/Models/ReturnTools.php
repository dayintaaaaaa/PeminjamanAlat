<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTools extends Model
{
    protected $table = 'return_tools';

    protected $fillable = [
        'return_code', 'id_borrowing', 'id_officer',
        'return_date', 'condition', 'status', 'notes',
    ];

    protected $casts = [
        'return_date' => 'date',
    ];

    public function borrowing()
    {
        return $this->belongsTo(Borrowing::class, 'id_borrowing');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'id_officer');
    }

    // jumlah hari terlambat: tanggal kembali dibanding jatuh tempo peminjaman
    public function getLateDaysAttribute(): int
    {
        $due = $this->borrowing?->due_date;

        if (! $due || ! $this->return_date) {
            return 0;
        }

        return $this->return_date->gt($due)
            ? (int) abs($due->diffInDays($this->return_date))
            : 0;
    }

    // Pending dan Rejected ditampilkan apa adanya; yang disetujui jadi On Time atau N Days Late
    public function getDisplayStatusAttribute(): string
    {
        if ($this->status !== 'Approved') {
            return $this->status;
        }

        return $this->late_days > 0 ? $this->late_days . ' Days Late' : 'On Time';
    }

    // class ditulis utuh supaya dikenali Tailwind
    public function statusClass(): string
    {
        return match (true) {
            $this->status === 'Pending'  => 'text-orange-500',
            $this->status === 'Rejected' => 'text-gray-500',
            $this->late_days > 0         => 'text-red-600',
            default                      => 'text-green-600',
        };
    }

    public function conditionClass(): string
    {
        return match ($this->condition) {
            'Good'    => 'text-green-600',
            'Damaged' => 'text-orange-500',
            default   => 'text-gray-900',
        };
    }

    // data untuk modal detail
    public function detailData(): array
    {
        $b = $this->borrowing;

        return [
            'borrower'       => $b?->borrower?->name ?? '-',
            'status'         => $this->display_status,
            'statusClass'    => $this->statusClass(),
            'condition'      => $this->condition,
            'conditionClass' => $this->conditionClass(),
            'code'           => $this->return_code,
            'returnAt'       => $this->created_at?->format('d-m-Y'),
            'tool'           => ($b?->tool?->tools_code ?? '-') . ' | ' . ($b?->tool?->tools_name ?? '-'),
            'category'       => ($b?->tool?->category?->category_code ?? '-') . ' | ' . ($b?->tool?->category?->category_name ?? '-'),
            'qty'            => (int) ($b?->borrow_qty ?? 0),
            'location'       => $b?->tool?->storage_location ?: '-',
            'photo'          => $b?->tool?->photo ? asset('storage/' . $b->tool->photo) : null,
            'borrowCode'     => $b?->borrow_code ?? '-',
            'borrowDate'     => $b?->borrow_date?->format('d-m-Y'),
            'returnDate'     => $this->return_date?->format('d-m-Y'),
            'dueDate'        => $b?->due_date?->format('d-m-Y'),
            'processedBy'    => $this->officer?->name ?? '-',
            'note'           => $this->notes ?: '-',
        ];
    }
}