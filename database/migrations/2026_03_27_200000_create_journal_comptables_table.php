<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('journal_comptables')) {
            Schema::create('journal_comptables', function (Blueprint $table) {
                $table->id();
                $table->string('code', 10)->unique();
                $table->string('libelle', 100);
                $table->string('type', 50);
                $table->text('description')->nullable();
                $table->string('couleur', 20)->nullable();
                $table->string('icone', 50)->nullable();
                $table->boolean('actif')->default(true);
                $table->boolean('systeme')->default(false);
                $table->timestamps();
            });

            DB::table('journal_comptables')->insert([
                [
                    'code' => 'AC',
                    'libelle' => 'Journal des achats',
                    'type' => 'achat',
                    'description' => 'Achats et charges fournisseurs',
                    'couleur' => '#dc3545',
                    'icone' => 'fas fa-shopping-cart',
                    'actif' => true,
                    'systeme' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'VT',
                    'libelle' => 'Journal des ventes',
                    'type' => 'vente',
                    'description' => 'Factures et produits',
                    'couleur' => '#198754',
                    'icone' => 'fas fa-file-invoice-dollar',
                    'actif' => true,
                    'systeme' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'BQ',
                    'libelle' => 'Journal de banque',
                    'type' => 'banque',
                    'description' => 'Encaissements et décaissements bancaires',
                    'couleur' => '#0d6efd',
                    'icone' => 'fas fa-university',
                    'actif' => true,
                    'systeme' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'CA',
                    'libelle' => 'Journal de caisse',
                    'type' => 'caisse',
                    'description' => 'Mouvements de caisse',
                    'couleur' => '#fd7e14',
                    'icone' => 'fas fa-cash-register',
                    'actif' => true,
                    'systeme' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'OD',
                    'libelle' => 'Opérations diverses',
                    'type' => 'od',
                    'description' => 'Écritures manuelles et reclassements',
                    'couleur' => '#6c757d',
                    'icone' => 'fas fa-book',
                    'actif' => true,
                    'systeme' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'PA',
                    'libelle' => 'Journal de paie',
                    'type' => 'paie',
                    'description' => 'Charges salariales et sociales',
                    'couleur' => '#6610f2',
                    'icone' => 'fas fa-users',
                    'actif' => true,
                    'systeme' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('journal_comptables');
    }
};