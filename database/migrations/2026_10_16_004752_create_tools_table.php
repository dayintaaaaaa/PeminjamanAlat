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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_category_tools')->constrained('tool_categories')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('tools_code', 20)->unique();
            $table->string('tools_name');
            $table->unsignedInteger('tools_qty')->default(0);
            $table->unsignedInteger('available')->default(0);
            $table->enum('status', ['Available', 'Borrowed', 'Maintenance', 'Broken'])->default('Available');
            $table->string('storage_location', 50);
            $table->text('description')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
