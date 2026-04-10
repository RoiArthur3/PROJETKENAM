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
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('nom_compte'); // Nom du compte bancaire
            $table->string('numero_compte')->unique();
            $table->string('banque'); // Nom de la banque
            $table->decimal('solde_initial', 15, 2)->default(0);
            $table->decimal('solde_actuel', 15, 2)->default(0);
            $table->enum('type', ['courant', 'epargne', 'entreprise'])->default('courant');
            $table->boolean('actif')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
