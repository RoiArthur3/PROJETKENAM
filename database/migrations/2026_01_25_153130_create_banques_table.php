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
        Schema::create('banques', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code_banque', 50)->nullable();
            $table->string('code_guichet', 50)->nullable();
            $table->string('adresse')->nullable();
            $table->string('ville', 100)->nullable();
            $table->string('pays', 100)->nullable();
            $table->string('telephone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('site_web')->nullable();
            $table->text('description')->nullable();
            $table->boolean('est_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Index
            $table->unique('code_banque');
            $table->index('nom');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('banques');
    }
};
