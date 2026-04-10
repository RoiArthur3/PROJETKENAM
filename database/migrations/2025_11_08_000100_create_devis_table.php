<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->string('client_name')->nullable();
            $table->string('contrat_ref', 100)->nullable();
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->string('objet')->nullable();
            $table->decimal('montant_ht', 14, 2)->default(0);
            $table->decimal('tva', 14, 2)->default(0);
            $table->decimal('total_ttc', 14, 2)->default(0);
            $table->text('notes')->nullable();
            $table->string('statut', 30)->default('brouillon');
            $table->timestamps();
            $table->index(['client_id','issue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devis');
    }
};
