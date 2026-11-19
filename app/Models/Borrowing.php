<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    protected $fillable = [
        'borrow_code','id_user', 'id_officer', 'id_tools', 'borrow_qty', 
        'borrow_date', 'due_date','status', 'notes', 
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date'    => 'date',
    ];

    public function borrower()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function tool()
    {
        return $this->belongsTo(Tool::class, 'id_tools');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'id_officer');
    }

    public function returns()
    {
        return $this->hasMany(ReturnTools::class, 'id_borrowing');
    }

    // pengajuan pengembalian yang masih menunggu verifikasi petugas
    public function pendingReturn()
    {
        return $this->hasOne(ReturnTools::class, 'id_borrowing')->where('status', 'Pending');
    }

    // alias supaya view dan controller yang membaca $borrowing->qty tetap jalan
    public function getQtyAttribute(): int
    {
        return (int) $this->borrow_qty;
    }

    // pengajuan return terbaru, apa pun statusnya
    public function latestReturn()
    {
        return $this->hasOne(ReturnTools::class, 'id_borrowing')->latestOfMany();
    }

    public function getDisplayStatusAttribute(): string
    {
        if ($this->status !== 'Active') {
            return $this->status;
        }

        if ($this->pendingReturn) {
            return 'Pending Return';
        }

        if ($this->latestReturn?->status === 'Rejected') {
            return 'Return Rejected';
        }

        if ($this->due_date && $this->due_date->lt(today())) {
            return 'Overdue';
        }

        return 'Active';
    }

    public function statusClass(): string
    {
        return match ($this->display_status) {
            'Pending', 'Pending Return'  => 'text-orange-500',
            'Active'                     => 'text-green-600',
            'Overdue', 'Return Rejected' => 'text-red-600',
            default                      => 'text-gray-500',
        };
    }

    // data untuk modal detail
    public function detailData(): array
    {
        return [
            'borrower'    => $this->borrower?->name ?? '-',
            'status'      => $this->display_status,
            'statusClass' => $this->statusClass(),
            'code'        => $this->borrow_code,
            'created'     => $this->created_at?->format('d-m-Y'),
            'tool'        => ($this->tool?->tools_code ?? '-') . ' | ' . ($this->tool?->tools_name ?? '-'),
            'category'    => ($this->tool?->category?->category_code ?? '-') . ' | ' . ($this->tool?->category?->category_name ?? '-'),
            'qty'         => (int) $this->borrow_qty,
            'location'    => $this->tool?->storage_location ?: '-',
            'photo'       => $this->tool?->photo ? asset('storage/' . $this->tool->photo) : null,
            'borrowDate'  => $this->borrow_date?->format('d-m-Y'),
            'dueDate'     => $this->due_date?->format('d-m-Y'),
            'processedBy' => $this->officer?->name ?? '-',
            'note'        => $this->notes ?: '-',
        ];
    }
}