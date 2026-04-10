<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (Schema::hasTable('notification_preferences')) {
            Schema::table('notification_preferences', function (Blueprint $table) {
                if (!Schema::hasColumn('notification_preferences', 'whatsapp_enabled')) {
                    $table->boolean('whatsapp_enabled')->default(false);
                }
                if (!Schema::hasColumn('notification_preferences', 'whatsapp_number')) {
                    $table->string('whatsapp_number')->nullable();
                }
                if (!Schema::hasColumn('notification_preferences', 'whatsapp_events')) {
                    $table->json('whatsapp_events')->nullable();
                }
            });
        }
    }

    public function down()
    {
        Schema::table('notification_preferences', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_enabled', 'whatsapp_number', 'whatsapp_events']);
        });
    }
};
