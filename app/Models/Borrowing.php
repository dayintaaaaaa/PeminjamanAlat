<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    protected $fillable = [
        'borrow_code', 'id_user', 'id_tool', 'qty',
        'borrow_date', 'due_date', 'status', 'notes', 'processed_by',
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
        return $this->belongsTo(Tool::class, 'id_tool');
    }

    public function officer()
    {
        return $this->belongsTo(User::class, 'processed_by');
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

    // Pending Return dan Overdue dihitung, bukan disimpan
    public function getDisplayStatusAttribute(): string
    {
        if ($this->status !== 'Active') {
            return $this->status;
        }

        if ($this->pendingReturn) {
            return 'Pending Return';
        }

        if ($this->due_date && $this->due_date->lt(today())) {
            return 'Overdue';
        }

        return 'Active';
    }
}