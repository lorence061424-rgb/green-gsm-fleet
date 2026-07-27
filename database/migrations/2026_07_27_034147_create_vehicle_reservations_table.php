<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vehicle Reservations table for VRDS module
        Schema::create('vehicle_reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('requested_by')->constrained('users')->onDelete('cascade'); // user who made the reservation
            $table->string('purpose'); // e.g. "Client Pickup", "Airport Transfer", "Scheduled Route"
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'cancelled'])->default('pending');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_reservations');
    }
};
