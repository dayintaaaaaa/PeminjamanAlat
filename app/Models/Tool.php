<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    protected $fillable = [
        'id_category_tools', 'tools_code', 'tools_name', 'tools_qty',
        'available', 'tools_condition', 'status', 'storage_location',
        'description', 'photo',
    ];

    public function category()
    {
        return $this->belongsTo(ToolCategory::class, 'id_category_tools');
    }
}