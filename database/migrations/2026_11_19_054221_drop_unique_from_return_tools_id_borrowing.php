<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('return_tools', function (Blueprint $table) {
            $table->index('id_borrowing');   // index biasa dulu, karena foreign key butuh index
            $table->dropUnique('return_tools_id_borrowing_unique');
        });
    }

    public function down(): void
    {
        Schema::table('return_tools', function (Blueprint $table) {
            $table->unique('id_borrowing');
            $table->dropIndex(['id_borrowing']);
        });
    }
};
