<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('paies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('mois');
            $table->unsignedSmallInteger('annee');
            $table->decimal('salaire_base', 12, 2)->default(0);
            $table->decimal('heures_sup', 12, 2)->default(0);
            $table->decimal('primes', 12, 2)->default(0);
            $table->decimal('brut', 12, 2)->default(0);
            $table->decimal('cnps_salariale', 12, 2)->default(0);
            $table->decimal('cnps_patronale', 12, 2)->default(0);
            $table->decimal('autres_retenues', 12, 2)->default(0);
            $table->decimal('net_a_payer', 12, 2)->default(0);
            $table->string('statut')->default('genere');
            $table->date('date_paiement')->nullable();
            $table->text('commentaires')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paies');
    }
};
