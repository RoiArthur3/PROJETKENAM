<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Operation;
use App\Models\Validation;
use App\Models\User;

class OperationValidationTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();
        
        if (!$user) {
            $this->command->warn('Aucun utilisateur trouvé. Créez au moins un utilisateur.');
            return;
        }

        // Vérifier que les types et services existent
        $typeOperation = \App\Models\TypeOperation::where('actif', true)->first();
        $operationalService = \App\Models\OperationalService::where('actif', true)->first();
        
        if (!$typeOperation) {
            $this->command->warn('Aucun type d\'opération actif trouvé. Créez-en un dans Paramétrage.');
            return;
        }
        
        if (!$operationalService) {
            $this->command->warn('Aucun service opérationnel actif trouvé. Créez-en un dans Paramétrage.');
            return;
        }

        // Opération 1: Achat matériel informatique
        $op1 = Operation::create([
            'titre' => 'Achat matériel informatique',
            'description' => 'Achat de 5 ordinateurs portables HP pour le service commercial',
            'priorite' => 'haute',
            'type_operation_id' => $typeOperation->id,
            'operational_service_id' => $operationalService->id,
            'statut_courant' => 'pending_validation',
            'demandeur_name' => $user->name,
            'demandeur_email' => $user->email,
        ]);

        Validation::create([
            'module_source' => 'operations',
            'record_id' => $op1->id,
            'type' => 'operation',
            'titre' => $op1->titre,
            'description' => $op1->description,
            'initiateur_id' => $user->id,
            'validateur_id' => null,
            'statut' => 'en_attente',
        ]);

        // Opération 2: Réparation véhicule
        $op2 = Operation::create([
            'titre' => 'Réparation véhicule VH-003',
            'description' => 'Réparation moteur et changement de pneus pour le véhicule VH-003',
            'priorite' => 'urgente',
            'type_operation_id' => $typeOperation->id,
            'operational_service_id' => $operationalService->id,
            'statut_courant' => 'pending_validation',
            'demandeur_name' => $user->name,
            'demandeur_email' => $user->email,
        ]);

        Validation::create([
            'module_source' => 'operations',
            'record_id' => $op2->id,
            'type' => 'maintenance',
            'titre' => $op2->titre,
            'description' => $op2->description,
            'initiateur_id' => $user->id,
            'validateur_id' => null,
            'statut' => 'en_attente',
        ]);

        // Opération 3: Commande fournitures bureau
        $op3 = Operation::create([
            'titre' => 'Commande fournitures de bureau',
            'description' => 'Commande de ramettes de papier, stylos et classeurs pour le trimestre',
            'priorite' => 'moyenne',
            'type_operation_id' => $typeOperation->id,
            'operational_service_id' => $operationalService->id,
            'statut_courant' => 'pending_validation',
            'demandeur_name' => $user->name,
            'demandeur_email' => $user->email,
        ]);

        Validation::create([
            'module_source' => 'operations',
            'record_id' => $op3->id,
            'type' => 'achat',
            'titre' => $op3->titre,
            'description' => $op3->description,
            'initiateur_id' => $user->id,
            'validateur_id' => null,
            'statut' => 'en_attente',
        ]);

        // Opération 4: Mission déplacement
        $op4 = Operation::create([
            'titre' => 'Mission déplacement Abidjan-Bouaké',
            'description' => 'Déplacement pour audit site client à Bouaké (2 jours)',
            'priorite' => 'haute',
            'type_operation_id' => $typeOperation->id,
            'operational_service_id' => $operationalService->id,
            'statut_courant' => 'pending_validation',
            'demandeur_name' => $user->name,
            'demandeur_email' => $user->email,
        ]);

        Validation::create([
            'module_source' => 'operations',
            'record_id' => $op4->id,
            'type' => 'mission',
            'titre' => $op4->titre,
            'description' => $op4->description,
            'initiateur_id' => $user->id,
            'validateur_id' => null,
            'statut' => 'en_cours',
        ]);

        // Opération 5: Contrat prestation
        $op5 = Operation::create([
            'titre' => 'Nouveau contrat prestation nettoyage',
            'description' => 'Signature contrat annuel avec société de nettoyage CLEAN PRO',
            'priorite' => 'moyenne',
            'type_operation_id' => $typeOperation->id,
            'operational_service_id' => $operationalService->id,
            'statut_courant' => 'pending_validation',
            'demandeur_name' => $user->name,
            'demandeur_email' => $user->email,
        ]);

        Validation::create([
            'module_source' => 'operations',
            'record_id' => $op5->id,
            'type' => 'contrat',
            'titre' => $op5->titre,
            'description' => $op5->description,
            'initiateur_id' => $user->id,
            'validateur_id' => null,
            'statut' => 'en_attente',
        ]);

        $this->command->info('5 opérations de test créées avec leurs validations.');
    }
}
