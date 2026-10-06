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
        Schema::table('vehicles', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicles', 'dealership_company')) {
                $table->string('dealership_company')->nullable()->after('make');
            }
            if (!Schema::hasColumn('vehicles', 'or_number')) {
                $table->string('or_number')->nullable()->after('dealership_company');
            }
            if (!Schema::hasColumn('vehicles', 'cr_number')) {
                $table->string('cr_number')->nullable()->after('or_number');
            }
            if (!Schema::hasColumn('vehicles', 'or_cr_expiry_date')) {
                $table->date('or_cr_expiry_date')->nullable()->after('cr_number');
            }
            if (!Schema::hasColumn('vehicles', 'registration_number')) {
                $table->string('registration_number')->nullable()->after('or_cr_expiry_date');
            }
            if (!Schema::hasColumn('vehicles', 'registration_date')) {
                $table->date('registration_date')->nullable()->after('registration_number');
            }
            if (!Schema::hasColumn('vehicles', 'registration_expiry_date')) {
                $table->date('registration_expiry_date')->nullable()->after('registration_date');
            }
            if (!Schema::hasColumn('vehicles', 'registration_status')) {
                $table->string('registration_status')->default('active')->after('registration_expiry_date');
            }
            if (!Schema::hasColumn('vehicles', 'fuel_type')) {
                $table->string('fuel_type')->default('Unleaded 91')->after('fuel_capacity');
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (!Schema::hasColumn('trips', 'toll_fees_amount')) {
                $table->decimal('toll_fees_amount', 10, 2)->default(0.00)->after('actual_fuel_liters');
            }
            if (!Schema::hasColumn('trips', 'toll_fees_breakdown')) {
                $table->text('toll_fees_breakdown')->nullable()->after('toll_fees_amount');
            }
        });

        Schema::table('maintenance_records', function (Blueprint $table) {
            if (!Schema::hasColumn('maintenance_records', 'repair_category')) {
                $table->string('repair_category')->default('maintenance')->after('service_type');
            }
            if (!Schema::hasColumn('maintenance_records', 'filter_status')) {
                $table->string('filter_status')->default('active')->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $columns = [
                'dealership_company', 'or_number', 'cr_number', 'or_cr_expiry_date',
                'registration_number', 'registration_date', 'registration_expiry_date',
                'registration_status', 'fuel_type'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('vehicles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('trips', function (Blueprint $table) {
            if (Schema::hasColumn('trips', 'toll_fees_amount')) {
                $table->dropColumn('toll_fees_amount');
            }
            if (Schema::hasColumn('trips', 'toll_fees_breakdown')) {
                $table->dropColumn('toll_fees_breakdown');
            }
        });

        Schema::table('maintenance_records', function (Blueprint $table) {
            if (Schema::hasColumn('maintenance_records', 'repair_category')) {
                $table->dropColumn('repair_category');
            }
            if (Schema::hasColumn('maintenance_records', 'filter_status')) {
                $table->dropColumn('filter_status');
            }
        });
    }
};
