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
        Schema::create('return_tools', function (Blueprint $table) {
             $table->id();
        $table->foreignId('id_borrowing')->unique()->constrained('borrowings')->cascadeOnDelete()->cascadeOnUpdate();
        $table->foreignId('id_officer')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
        $table->string('return_code', 50)->unique();                  // RET-02
        $table->date('return_date');
        $table->enum('condition', ['Good', 'Damaged', 'Lost'])->default('Good');
        $table->unsignedInteger('late_days')->default(0);
        $table->text('notes')->nullable();
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_tools');
    }
};
