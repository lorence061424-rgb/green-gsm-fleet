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
        // 1. Vehicles
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('license_plate')->unique();
            $table->string('model');
            $table->string('make');
            $table->integer('year');
            $table->enum('type', ['Sedan', 'SUV', 'Van', 'Hatchback', 'Truck', 'Crossover']);
            $table->enum('status', ['active', 'maintenance', 'offline'])->default('active');
            $table->decimal('fuel_capacity', 8, 2);
            $table->decimal('current_gps_lat', 10, 8)->nullable();
            $table->decimal('current_gps_lng', 11, 8)->nullable();
            $table->timestamps();
        });

        // 2. Drivers
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('license_number')->unique();
            $table->enum('status', ['available', 'on_trip', 'offline'])->default('available');
            $table->decimal('performance_score', 5, 2)->default(100.00);
            $table->integer('total_trips')->default(0);
            $table->decimal('total_distance_km', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // 3. Trips
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('booking_reference_id')->nullable();
            $table->foreignId('driver_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('vehicle_id')->nullable()->constrained()->onDelete('set null');
            $table->string('start_location');
            $table->string('end_location');
            $table->decimal('start_lat', 10, 8);
            $table->decimal('start_lng', 11, 8);
            $table->decimal('end_lat', 10, 8);
            $table->decimal('end_lng', 11, 8);
            $table->enum('status', ['scheduled', 'active', 'completed', 'cancelled'])->default('scheduled');
            $table->decimal('distance_km', 8, 2);
            $table->integer('estimated_duration_minutes');
            $table->integer('actual_duration_minutes')->nullable();
            $table->decimal('estimated_fuel_liters', 8, 2)->nullable();
            $table->decimal('actual_fuel_liters', 8, 2)->nullable();
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable();
            $table->timestamps();
        });

        // 4. Trip Logs (GPS Telemetry)
        Schema::create('trip_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->decimal('speed_kmh', 5, 2);
            $table->integer('idle_time_seconds')->default(0);
            $table->timestamp('timestamp')->useCurrent();
        });

        // 5. Fuel Logs
        Schema::create('fuel_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('trip_id')->nullable()->constrained()->onDelete('set null');
            $table->date('date');
            $table->decimal('amount_liters', 8, 2);
            $table->decimal('cost', 10, 2);
            $table->decimal('odometer_reading', 10, 2);
            $table->string('fuel_type')->default('Gasoline');
            $table->timestamps();
        });

        // 6. Maintenance Records
        Schema::create('maintenance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->string('service_type');
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2);
            $table->enum('status', ['scheduled', 'in_progress', 'completed'])->default('scheduled');
            $table->date('scheduled_date');
            $table->date('completion_date')->nullable();
            $table->timestamps();
        });

        // 7. Performance Records
        Schema::create('performance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('trip_id')->constrained()->onDelete('cascade');
            $table->integer('speeding_events')->default(0);
            $table->integer('harsh_braking_events')->default(0);
            $table->decimal('idle_minutes', 5, 2)->default(0.00);
            $table->integer('safety_score')->default(100);
            $table->timestamps();
        });

        // 8. Routes
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->string('start_point');
            $table->string('end_point');
            $table->decimal('distance_km', 8, 2);
            $table->json('optimized_path'); // Array of lat/lng coordinate arrays
            $table->decimal('avg_fuel_consumption', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
        Schema::dropIfExists('performance_records');
        Schema::dropIfExists('maintenance_records');
        Schema::dropIfExists('fuel_logs');
        Schema::dropIfExists('trip_logs');
        Schema::dropIfExists('trips');
        Schema::dropIfExists('drivers');
        Schema::dropIfExists('vehicles');
    }
};
