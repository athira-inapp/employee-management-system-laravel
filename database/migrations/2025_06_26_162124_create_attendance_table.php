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
        Schema::create('attendance', function (Blueprint $table) {
            $table->id(); // Laravel convention PK
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('time_in')->nullable();
            $table->time('time_out')->nullable();
            $table->decimal('hours_worked', 4, 2)->nullable();
            $table->enum('status', ['present', 'absent', 'late', 'half_day'])->default('present');
            $table->timestamps();

            // Indexes for performance
            $table->index(['employee_id', 'date']);
            $table->index(['date']);
            $table->index(['status']);

            // Unique constraint to prevent duplicate attendance records
            $table->unique(['employee_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance');
    }
};
