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
        Schema::table('entreprise_settings', function (Blueprint $table) {
            $table->string('sms_provider')->nullable()->after('email_support');
            $table->string('sms_api_key')->nullable()->after('sms_provider');
            $table->string('sms_api_secret', 500)->nullable()->after('sms_api_key');
            $table->string('sms_sender_id')->nullable()->after('sms_api_secret');
            $table->string('sms_api_url')->nullable()->after('sms_sender_id');
            $table->boolean('sms_is_active')->default(false)->after('sms_api_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_settings', function (Blueprint $table) {
            $table->dropColumn([
                'sms_provider',
                'sms_api_key',
                'sms_api_secret',
                'sms_sender_id',
                'sms_api_url',
                'sms_is_active'
            ]);
        });
    }
};
