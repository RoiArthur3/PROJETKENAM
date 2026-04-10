<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class CreateMissingTablesSeeder extends Seeder
{
    public function run(): void
    {
        if (!Schema::hasTable('banques')) {
            Schema::create('banques', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('code_banque')->unique()->nullable();
                $table->string('adresse')->nullable();
                $table->string('telephone')->nullable();
                $table->string('email')->nullable();
                $table->timestamps();
            });
            $this->command->info('✅ Table banques created');
        }

        if (!Schema::hasTable('compte_bancaires')) {
            Schema::create('compte_bancaires', function (Blueprint $table) {
                $table->id();
                $table->string('numero_compte')->unique();
                $table->string('titulaire');
                $table->foreignId('banque_id')->constrained('banques')->onDelete('cascade');
                $table->enum('type', ['courant', 'epargne', 'titre'])->default('courant');
                $table->decimal('solde', 15, 2)->default(0);
                $table->timestamps();
                $table->softDeletes();
            });
            $this->command->info('✅ Table compte_bancaires created');
        }

        if (!Schema::hasTable('virements')) {
            Schema::create('virements', function (Blueprint $table) {
                $table->id();
                $table->string('reference')->unique();
                $table->foreignId('compte_source_id')->constrained('compte_bancaires')->onDelete('restrict');
                $table->foreignId('compte_destination_id')->constrained('compte_bancaires')->onDelete('restrict');
                $table->decimal('montant', 15, 2);
                $table->date('date_virement');
                $table->enum('statut', ['en_attente', 'effectue', 'annule', 'echec'])->default('en_attente');
                $table->string('motif')->nullable();
                $table->text('notes')->nullable();
                $table->foreignId('initie_par')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('valide_par')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('date_validation')->nullable();
                $table->string('reference_operation')->nullable();
                $table->decimal('frais', 15, 2)->default(0);
                $table->string('devise', 3)->default('XOF');
                $table->decimal('taux_change', 15, 6)->default(1);
                $table->timestamps();
                $table->softDeletes();
                $table->index('reference');
                $table->index('date_virement');
                $table->index('statut');
            });
            $this->command->info('✅ Table virements created');
        }
    }
}
