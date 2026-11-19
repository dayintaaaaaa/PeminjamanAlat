<?php

namespace App\Services;

use App\Models\Tool;

class StockService
{
    // alat keluar (dipinjam)
    public function lend(Tool $tool, int $qty): void
    {
        $tool->available = max(0, (int) $tool->available - $qty);
        $this->sync($tool);
    }

    // pinjaman dibatalkan atau diubah: stok kembali
    public function restoreLend(Tool $tool, int $qty): void
    {
        $tool->available = (int) $tool->available + $qty;
        $this->sync($tool);
    }

    // pengembalian disetujui
    public function applyReturn(Tool $tool, int $qty, string $condition): void
    {
        if ($condition === 'Lost') {
            // alat hilang: jumlah total berkurang, stok tersedia tidak bertambah
            $tool->tools_qty = max(0, (int) $tool->tools_qty - $qty);
        } else {
            $tool->available = (int) $tool->available + $qty;

            if ($condition === 'Damaged') {
                $tool->tools_condition = 'Damaged';
            }
        }

        $this->sync($tool);
    }

    // pengembalian dibatalkan atau diubah: kebalikan dari applyReturn
    public function revertReturn(Tool $tool, int $qty, string $condition): void
    {
        if ($condition === 'Lost') {
            $tool->tools_qty = (int) $tool->tools_qty + $qty;
        } else {
            $tool->available = max(0, (int) $tool->available - $qty);
        }

        $this->sync($tool);
    }

    private function sync(Tool $tool): void
    {
        // tersedia tidak boleh melebihi total
        $tool->available = max(0, min((int) $tool->available, (int) $tool->tools_qty));

        if ($tool->status !== 'Maintenance') {
            $tool->status = $tool->available > 0 ? 'Available' : 'Borrowed';
        }

        $tool->save();
    }
}