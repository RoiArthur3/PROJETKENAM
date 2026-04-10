<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add whatsapp to the enum type if it doesn't exist
            $table->enum('type', ['email', 'sms', 'whatsapp'])->default('email')->change();
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->enum('type', ['email', 'sms'])->default('email')->change();
        });
    }
};
