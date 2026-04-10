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
        Schema::create('entreprise_settings', function (Blueprint $table) {
            $table->id();
            $table->string('nom_entreprise')->nullable();
            $table->string('sigle')->nullable();
            $table->text('adresse')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email_contact')->nullable();
            $table->string('site_web')->nullable();
            $table->string('rccm')->nullable();
            $table->string('compte_bancaire')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('ifu')->nullable();
            $table->string('cnss')->nullable();
            $table->string('email_noreply')->nullable();
            $table->string('email_support')->nullable();
            $table->string('telephone_support')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entreprise_settings');
    }
};
