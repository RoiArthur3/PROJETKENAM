<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('sms_enabled')->default(false);
            $table->string('phone_number')->nullable();
            $table->json('sms_events')->nullable(); // Events pour lesquels envoyer SMS
            $table->boolean('email_enabled')->default(true);
            $table->json('email_events')->nullable(); // Events pour lesquels envoyer email
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notification_preferences');
    }
};
