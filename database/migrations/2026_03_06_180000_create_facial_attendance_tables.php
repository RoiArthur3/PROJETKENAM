<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('facial_devices')) {
            Schema::create('facial_devices', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('serial_number')->unique();
                $table->string('ip_address', 64);
                $table->unsignedSmallInteger('port')->default(80);
                $table->string('protocol', 10)->default('http');
                $table->string('username')->nullable();
                $table->string('password')->nullable();
                $table->string('api_token', 80)->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamp('last_seen_at')->nullable();
                $table->string('last_status', 30)->default('unknown');
                $table->text('last_error')->nullable();
                $table->json('settings')->nullable();
                $table->timestamps();

                $table->index(['is_active', 'last_status']);
            });
        }

        if (!Schema::hasTable('facial_events')) {
            Schema::create('facial_events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('facial_device_id')->nullable()->constrained('facial_devices')->nullOnDelete();
                $table->string('serial_number', 100)->nullable();
                $table->string('employee_code', 100)->nullable();
                $table->string('employee_name')->nullable();
                $table->dateTime('event_time');
                $table->string('event_type', 50)->default('authentication');
                $table->string('direction', 20)->default('unknown');
                $table->string('status', 30)->default('success');
                $table->decimal('confidence', 5, 2)->nullable();
                $table->json('raw_payload')->nullable();
                $table->timestamp('processed_at')->nullable();
                $table->string('processing_status', 20)->default('pending');
                $table->text('processing_message')->nullable();
                $table->unsignedBigInteger('pointage_id')->nullable();
                $table->timestamps();

                $table->index(['event_time', 'processing_status']);
                $table->index(['employee_code', 'event_time']);
                $table->index('serial_number');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('facial_events');
        Schema::dropIfExists('facial_devices');
    }
};
