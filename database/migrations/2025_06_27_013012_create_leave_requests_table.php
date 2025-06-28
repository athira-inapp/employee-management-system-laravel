<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->enum('leave_type', ['sick', 'vacation', 'personal', 'emergency', 'maternity', 'paternity']);
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('days_requested');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('employees')->onDelete('set null');
            $table->text('manager_comments')->nullable();
            $table->timestamp('request_date')->useCurrent();
            $table->timestamp('response_date')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['employee_id']);
            $table->index(['status']);
            $table->index(['leave_type']);
            $table->index(['start_date', 'end_date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('leave_requests');
    }
};
