<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('encaissements')) {
            return;
        }

        Schema::create('encaissements', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->date('date_encaissement');
            $table->string('type_encaissement');
            $table->decimal('montant', 15, 2);
            $table->string('mode_paiement')->nullable();
            $table->string('client')->nullable();
            $table->foreignId('caisse_id')->nullable()->constrained('caisses')->nullOnDelete();
            $table->text('description')->nullable();
            $table->string('statut')->default('validé');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('encaissements');
    }
};
