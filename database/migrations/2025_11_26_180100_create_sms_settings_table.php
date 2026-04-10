<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->default('custom'); // mtn, orange, twilio, nexmo, custom
            $table->string('sender_id')->nullable();
            $table->string('api_url')->nullable();
            $table->string('api_username')->nullable();
            $table->string('api_password')->nullable();
            $table->boolean('sms_enabled')->default(false);
            $table->boolean('sandbox_mode')->default(true);
            $table->text('default_template')->nullable();
            $table->string('fallback_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_settings');
    }
};
