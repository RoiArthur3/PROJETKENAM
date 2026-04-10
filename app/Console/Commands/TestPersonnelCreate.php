<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Personnel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class TestPersonnelCreate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:personnel-create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test creating a personnel record directly using Eloquent';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing personnel creation...');
        
        $validated = [
            'matricule' => 'KSL' . time(),
            'nom' => 'Test Command',
            'prenoms' => 'Test',
            'date_naissance' => '2000-01-01',
            'lieu_naissance' => 'Abidjan',
            'nationalite' => 'Ivoirienne',
            'sexe' => 'M',
            'situation_matrimoniale' => 'Celibataire',
            'nb_enfants_charge' => 0,
            
            // Coordonnées
            'telephone_principal' => '0102030405',
            'adresse_residence' => 'Abidjan',
            'ville' => 'Abidjan',
            
            // Pièce d'identité
            'type_piece' => 'CNI',
            'numero_piece' => 'CI' . time(),
            'date_delivrance_piece' => '2020-01-01',
            'lieu_delivrance_piece' => 'Abidjan',
            
            // Professionnel
            'poste' => 'Developpeur',
            'service' => 'Informatique',
            'categorie' => 'Cadre',
            
            // Contrat
            'type_contrat' => 'CDI',
            'date_embauche' => '2024-01-01',
            'duree_essai_jours' => 90,
            'salaire_base' => 500000,
            'devise' => 'XOF',
            'frequence_paiement' => 'MENSUEL',
            
            // Fiscalité
            'nb_parts_fiscales' => 1,
            'situation_fiscale' => 'IMPOSABLE',
            
            // Contact d'urgence
            'nom_urgence' => 'Test',
            'telephone_urgence' => '0102030405',
            'lien_parente' => 'Ami',
            'created_by' => 1
        ];

        try {
            DB::beginTransaction();

            // Calcul de la fin de période d'essai
            $validated['fin_periode_essai'] = Carbon::parse($validated['date_embauche'])
                ->addDays((int)$validated['duree_essai_jours']);

            // Vérifier si la table personnel existe
            if (!Schema::hasTable('personnel')) {
                DB::rollBack();
                $this->error('La table personnel n\'existe pas.');
                return;
            }

            $personnel = Personnel::create($validated);

            DB::commit();

            $this->info('Success! Personnel ID: ' . $personnel->id);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Error: ' . $e->getMessage());
        }
    }
}
