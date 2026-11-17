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
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_user')->constrained('users')->cascadeOnDelete()->cascadeOnUpdate(); //peminjam
            $table->foreignId('id_officer')->nullable()->constrained('users')->nullOnDelete()->cascadeOnDelete(); //petugas
            $table->foreignId('id_tools')->constrained('tools')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('borrow_code', 50)->unique();
            $table->date('borrow_date');
            $table->unsignedInteger('borrow_qty');
            $table->date('due_date');
            $table->enum('status', ['Pending', 'Active', 'Overdue'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
