<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturnTools extends Model
{
    protected $table = 'return_tools';

    protected $fillable = [
        'return_code', 'id_borrowing', 'return_date',
        'return_condition', 'status', 'notes', 'processed_by',
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
        return $this->belongsTo(User::class, 'processed_by');
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
}