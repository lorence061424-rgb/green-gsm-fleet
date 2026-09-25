<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for Security Log Archives table.
     */
    public function up(): void
    {
        if (!Schema::hasTable('security_log_archives')) {
            Schema::create('security_log_archives', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('original_log_id')->nullable();
                $table->string('event_type');
                $table->string('email')->nullable();
                $table->string('ip_address')->nullable();
                $table->string('user_agent')->nullable();
                $table->text('details')->nullable();
                $table->timestamp('original_created_at')->nullable();
                $table->timestamp('archived_at')->useCurrent();
                $table->string('archived_by')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_log_archives');
    }
};
