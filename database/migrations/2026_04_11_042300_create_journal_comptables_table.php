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
        Schema::create('journal_comptables', function (Blueprint $table) {
            $table->id();
            $table->string('code', 10)->unique(); // Code du journal (ex: AC, VT, BQ, etc.)
            $table->string('libelle'); // Libellé du journal
            $table->string('type', 20); // Type: Achat, Vente, Trésorerie, OD
            $table->text('description')->nullable(); // Description du journal
            $table->string('couleur', 7)->default('#000000'); // Couleur pour l'affichage
            $table->string('icone', 50)->default('fas fa-book'); // Icône FontAwesome
            $table->boolean('actif')->default(true); // Journal actif ou non
            $table->boolean('systeme')->default(false); // Journal système (non modifiable)
            $table->timestamps();
            
            // Index
            $table->index('code');
            $table->index('type');
            $table->index('actif');
        });

        // Insertion des journaux comptables standards selon les pratiques comptables ivoiriennes
        DB::table('journal_comptables')->insert([
            [
                'code' => 'AC',
                'libelle' => 'Journal des Achats',
                'type' => 'Achat',
                'description' => 'Enregistrement des factures d\'achat et dépenses',
                'couleur' => '#dc3545',
                'icone' => 'fas fa-shopping-cart',
                'actif' => true,
                'systeme' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'VT',
                'libelle' => 'Journal des Ventes',
                'type' => 'Vente',
                'description' => 'Enregistrement des factures de vente et recettes',
                'couleur' => '#28a745',
                'icone' => 'fas fa-cash-register',
                'actif' => true,
                'systeme' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'BQ',
                'libelle' => 'Journal de Banque',
                'type' => 'Trésorerie',
                'description' => 'Mouvements bancaires et relevés de compte',
                'couleur' => '#007bff',
                'icone' => 'fas fa-university',
                'actif' => true,
                'systeme' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CA',
                'libelle' => 'Journal de Caisse',
                'type' => 'Trésorerie',
                'description' => 'Mouvements de caisse et espèces',
                'couleur' => '#fd7e14',
                'icone' => 'fas fa-money-bill',
                'actif' => true,
                'systeme' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'OD',
                'libelle' => 'Journal des Opérations Diverses',
                'type' => 'OD',
                'description' => 'Écritures diverses, régularisations et corrections',
                'couleur' => '#6f42c1',
                'icone' => 'fas fa-edit',
                'actif' => true,
                'systeme' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'AN',
                'libelle' => 'Journal des Nouveaux',
                'type' => 'OD',
                'description' => 'Écritures de nouvel exercice et à nouveaux',
                'couleur' => '#17a2b8',
                'icone' => 'fas fa-sync',
                'actif' => true,
                'systeme' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SI',
                'libelle' => 'Journal des Immobilisations',
                'type' => 'OD',
                'description' => 'Acquisition et cession d\'immobilisations',
                'couleur' => '#20c997',
                'icone' => 'fas fa-building',
                'actif' => true,
                'systeme' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'CP',
                'libelle' => 'Journal des Paies',
                'type' => 'OD',
                'description' => 'Salaires et charges sociales',
                'couleur' => '#e83e8c',
                'icone' => 'fas fa-users',
                'actif' => true,
                'systeme' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_comptables');
    }
};
