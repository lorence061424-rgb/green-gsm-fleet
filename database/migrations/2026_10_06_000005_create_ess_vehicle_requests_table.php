<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ess_vehicle_requests')) {
            Schema::create('ess_vehicle_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('employee_id');           // FK → users.id
                $table->enum('purpose_type', ['meeting', 'field_visit', 'airport', 'inter_branch', 'errand', 'emergency'])->default('meeting');
                $table->text('purpose_description');
                $table->string('destination', 255);
                $table->date('reservation_date');
                $table->time('start_time');
                $table->time('end_time');
                $table->tinyInteger('num_passengers')->unsigned()->default(1);
                $table->string('vehicle_preference', 100)->nullable();  // "SUV", "Van", "Sedan"
                $table->unsignedBigInteger('driver_preference')->nullable(); // FK → drivers.id
                $table->boolean('is_roundtrip')->default(true);
                $table->enum('urgency_level', ['routine', 'priority'])->default('routine');
                $table->text('additional_remarks')->nullable();
                $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
                $table->text('rejection_reason')->nullable();
                $table->unsignedBigInteger('assigned_vehicle_id')->nullable(); // FK → vehicles.id
                $table->unsignedBigInteger('assigned_driver_id')->nullable();  // FK → drivers.id
                $table->unsignedBigInteger('reviewed_by')->nullable();          // FK → users.id
                $table->unsignedBigInteger('reservation_id')->nullable();       // FK → vehicle_reservations.id
                $table->timestamps();

                $table->foreign('employee_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('reviewed_by')->references('id')->on('users')->onDelete('set null');
                $table->foreign('assigned_vehicle_id')->references('id')->on('vehicles')->onDelete('set null');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ess_vehicle_requests');
    }
};
