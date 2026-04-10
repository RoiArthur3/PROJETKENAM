<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Insérer les types d'opération par défaut
        DB::table('types_operations')->insert([
            [
                'code' => 'DEMANDE',
                'libelle' => 'Demande',
                'description' => 'Demande de service ou de ressources',
                'couleur' => '#007bff',
                'icone' => 'fas fa-paper-plane',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'APPROVISIONNEMENT',
                'libelle' => 'Approvisionnement',
                'description' => 'Demande d\'approvisionnement en matériel',
                'couleur' => '#28a745',
                'icone' => 'fas fa-box',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'MAINTENANCE',
                'libelle' => 'Maintenance',
                'description' => 'Demande de maintenance ou réparation',
                'couleur' => '#ffc107',
                'icone' => 'fas fa-wrench',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'TRANSPORT',
                'libelle' => 'Transport',
                'description' => 'Demande de transport ou véhicule',
                'couleur' => '#17a2b8',
                'icone' => 'fas fa-car',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'FORMATION',
                'libelle' => 'Formation',
                'description' => 'Demande de formation ou de coaching',
                'couleur' => '#6f42c1',
                'icone' => 'fas fa-graduation-cap',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ACHAT',
                'libelle' => 'Achat',
                'description' => 'Demande d\'achat de biens ou services',
                'couleur' => '#fd7e14',
                'icone' => 'fas fa-shopping-cart',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'INFORMATION',
                'libelle' => 'Information',
                'description' => 'Demande d\'information ou clarification',
                'couleur' => '#6c757d',
                'icone' => 'fas fa-info-circle',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'URGENT',
                'libelle' => 'Urgent',
                'description' => 'Demande urgente nécessitant un traitement prioritaire',
                'couleur' => '#dc3545',
                'icone' => 'fas fa-exclamation-triangle',
                'actif' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    public function down(): void
    {
        DB::table('types_operations')->whereIn('code', [
            'DEMANDE', 'APPROVISIONNEMENT', 'MAINTENANCE', 'TRANSPORT',
            'FORMATION', 'ACHAT', 'INFORMATION', 'URGENT'
        ])->delete();
    }
};
