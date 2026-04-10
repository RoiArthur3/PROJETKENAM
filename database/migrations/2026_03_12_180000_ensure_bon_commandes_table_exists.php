<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bon_commandes')) {
            Schema::create('bon_commandes', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->unique();
                $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
                $table->date('date_commande')->nullable();
                $table->date('date_livraison_prevue')->nullable();
                $table->unsignedInteger('duration_days')->nullable();
                $table->decimal('montant_total', 15, 2)->default(0);
                $table->string('statut')->default('en_attente');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->index('client_id');
                $table->index('statut');
                $table->index('date_commande');
            });

            return;
        }

        Schema::table('bon_commandes', function (Blueprint $table) {
            if (!Schema::hasColumn('bon_commandes', 'duration_days')) {
                $table->unsignedInteger('duration_days')->nullable()->after('date_livraison_prevue');
            }
        });
    }

    public function down(): void
    {
        // Ne pas dropper la table ici: migration de sécurité.
        Schema::table('bon_commandes', function (Blueprint $table) {
            if (Schema::hasColumn('bon_commandes', 'duration_days')) {
                $table->dropColumn('duration_days');
            }
        });
    }
};
