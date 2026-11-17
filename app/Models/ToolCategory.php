<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToolCategory extends Model
{
    protected $fillable = ['category_code', 'category_name', 'description'];

    public function tools()
    {
        return $this->hasMany(Tool::class, 'id_category_tools');
    }
}