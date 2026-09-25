<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for adding Customer Rating & Ride-Hailing Feedback columns to trips table.
     */
    public function up(): void
    {
        if (Schema::hasTable('trips')) {
            Schema::table('trips', function (Blueprint $table) {
                if (!Schema::hasColumn('trips', 'rating')) {
                    $table->decimal('rating', 2, 1)->nullable()->default(5.0)->after('actual_fuel_liters');
                }
                if (!Schema::hasColumn('trips', 'customer_feedback')) {
                    $table->text('customer_feedback')->nullable()->after('rating');
                }
                if (!Schema::hasColumn('trips', 'feedback_category')) {
                    $table->string('feedback_category')->nullable()->default('compliment')->after('customer_feedback');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('trips')) {
            Schema::table('trips', function (Blueprint $table) {
                $table->dropColumn(['rating', 'customer_feedback', 'feedback_category']);
            });
        }
    }
};
