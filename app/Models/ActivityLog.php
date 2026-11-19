<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ActivityLog extends Model
{
    protected $fillable = ['id_user', 'user_name', 'action', 'module', 'description'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // catat satu aktivitas; kegagalan mencatat tidak boleh menggagalkan aksi utamanya
    public static function record(string $action, string $module, string $description, ?User $user = null): void
    {
        try {
            $user ??= Auth::user();

            static::create([
                'id_user'     => $user?->id,
                'user_name'   => $user?->name ?? 'System',
                'action'      => $action,
                'module'      => $module,
                'description' => Str::limit($description, 480),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}