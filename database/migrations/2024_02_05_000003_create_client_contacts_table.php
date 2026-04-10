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
        Schema::create('client_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->enum('contact_type', ['primary', 'billing', 'technical', 'logistics'])->default('primary');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('position')->nullable();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->boolean('is_active')->default(true);
            $table->enum('preferred_contact_method', ['email', 'phone', 'sms'])->default('email');
            $table->string('language_preference')->default('fr');
            $table->string('timezone')->default('UTC');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['client_id', 'contact_type']);
            $table->index(['client_id', 'is_primary']);
            $table->index(['email']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_contacts');
    }
};
