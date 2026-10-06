<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ess_maintenance_requests')) {
            Schema::create('ess_maintenance_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');           // FK → users.id
                $table->unsignedBigInteger('vehicle_id');            // FK → vehicles.id
                $table->enum('request_type', ['repair', 'reimbursement', 'scheduled_maintenance'])->default('repair');
                $table->text('anomaly_description');
                $table->date('anomaly_date');
                $table->enum('urgency_level', ['low', 'medium', 'high', 'critical'])->default('medium');
                $table->unsignedInteger('odometer_reading')->nullable();
                $table->decimal('reimbursement_amount', 10, 2)->nullable();
                $table->string('receipt_number', 100)->nullable();
                $table->text('supporting_docs')->nullable();          // JSON array of file paths
                $table->text('remarks')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'in_progress', 'completed'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->string('assigned_shop', 255)->nullable();
                $table->decimal('estimated_cost', 10, 2)->nullable();
                $table->decimal('actual_cost', 10, 2)->nullable();
                $table->date('scheduled_repair_date')->nullable();
                $table->date('completed_date')->nullable();
                $table->unsignedBigInteger('reviewed_by')->nullable(); // FK → users.id
                $table->unsignedBigInteger('maintenance_record_id')->nullable(); // FK → maintenance_records.id
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('vehicle_id')->references('id')->on('vehicles')->onDelete('cascade');
                $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ess_maintenance_requests');
    }
};
