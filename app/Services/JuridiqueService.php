<?php

namespace App\Services;

use App\Models\JuridiqueContrat;
use App\Models\JuridiqueDocument;
use App\Models\FinancementDossier;
use App\Models\FinancementOffreBancaire;
use App\Models\FinancementEcheance;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class JuridiqueService
{
    /**
     * Vérifier les contrats expirant bientôt et envoyer des alertes
     */
    public function verifierContratsExpirantBientot($jours = 30)
    {
        $contrats = JuridiqueContrat::expirantBientot($jours)->get();
        
        foreach ($contrats as $contrat) {
            $this->envoyerAlerteExpirationContrat($contrat, $jours);
        }
        
        return $contrats->count();
    }

    /**
     * Vérifier les documents expirant bientôt
     */
    public function verifierDocumentsExpirantBientot($jours = 30)
    {
        $documents = JuridiqueDocument::expirantBientot($jours)->get();
        
        foreach ($documents as $document) {
            $this->envoyerAlerteExpirationDocument($document, $jours);
        }
        
        return $documents->count();
    }

    /**
     * Vérifier les échéances en retard
     */
    public function verifierEcheancesEnRetard()
    {
        $echeances = FinancementEcheance::enRetard()->get();
        
        foreach ($echeances as $echeance) {
            $this->envoyerAlerteEcheanceEnRetard($echeance);
        }
        
        return $echeances->count();
    }

    /**
     * Créer un dossier de financement à partir d'un contrat
     */
    public function creerDossierFinancementDepuisContrat(JuridiqueContrat $contrat, array $donnees)
    {
        $dossier = FinancementDossier::create([
            'reference' => $this->genererReference('FIN'),
            'intitule' => $donnees['intitule'] ?? 'Financement pour ' . $contrat->titre,
            'type_financement' => $donnees['type_financement'] ?? 'emprunt',
            'organisme_cible' => $donnees['organisme_cible'] ?? null,
            'montant_demande' => $donnees['montant_demande'] ?? $contrat->montant,
            'devise' => $donnees['devise'] ?? $contrat->devise,
            'description' => $donnees['description'] ?? 'Dossier créé à partir du contrat ' . $contrat->reference,
            'contrat_id' => $contrat->id,
            'created_by' => auth()->id(),
        ]);

        // Associer le contrat au dossier si nécessaire
        if ($dossier && $contrat) {
            Log::info('Dossier de financement créé depuis le contrat', [
                'contrat_id' => $contrat->id,
                'dossier_id' => $dossier->id,
                'user_id' => auth()->id(),
            ]);
        }

        return $dossier;
    }

    /**
     * Générer une référence unique
     */
    private function genererReference($prefixe)
    {
        $timestamp = now()->format('Ymd');
        $random = mt_rand(1000, 9999);
        return $prefixe . $timestamp . $random;
    }

    /**
     * Envoyer une alerte d'expiration de contrat
     */
    private function envoyerAlerteExpirationContrat(JuridiqueContrat $contrat, $jours)
    {
        $message = "Le contrat '{$contrat->titre}' expire dans {$jours} jours ({$contrat->date_fin->format('d/m/Y')})";
        
        Log::warning('Alerte expiration contrat', [
            'contrat_id' => $contrat->id,
            'jours_restants' => $jours,
            'date_expiration' => $contrat->date_fin,
        ]);

        // Envoyer email aux responsables
        $this->notifierResponsables('expiration_contrat', [
            'contrat' => $contrat,
            'jours' => $jours,
        ]);
    }

    /**
     * Envoyer une alerte d'expiration de document
     */
    private function envoyerAlerteExpirationDocument(JuridiqueDocument $document, $jours)
    {
        $message = "Le document '{$document->titre}' expire dans {$jours} jours ({$document->date_expiration->format('d/m/Y')})";
        
        Log::warning('Alerte expiration document', [
            'document_id' => $document->id,
            'jours_restants' => $jours,
            'date_expiration' => $document->date_expiration,
        ]);

        $this->notifierResponsables('expiration_document', [
            'document' => $document,
            'jours' => $jours,
        ]);
    }

    /**
     * Envoyer une alerte d'échéance en retard
     */
    private function envoyerAlerteEcheanceEnRetard(FinancementEcheance $echeance)
    {
        $joursRetard = now()->diffInDays($echeance->date_echeance);
        
        Log::warning('Alerte échéance en retard', [
            'echeance_id' => $echeance->id,
            'jours_retard' => $joursRetard,
            'montant' => $echeance->montant,
        ]);

        $this->notifierResponsables('echeance_retard', [
            'echeance' => $echeance,
            'jours_retard' => $joursRetard,
        ]);
    }

    /**
     * Notifier les responsables juridiques
     */
    private function notifierResponsables($type, $data)
    {
        $responsables = User::where('role', 'admin')
                           ->orWhere('role', 'superadmin')
                           ->where('is_active', true)
                           ->get();

        foreach ($responsables as $responsable) {
            try {
                // Envoyer notification (adapter selon votre système)
                // Mail::to($responsable->email)->send(new JuridiqueNotification($type, $data));
                
                Log::info('Notification envoyée', [
                    'user_id' => $responsable->id,
                    'type' => $type,
                    'email' => $responsable->email,
                ]);
            } catch (\Exception $e) {
                Log::error('Erreur envoi notification', [
                    'user_id' => $responsable->id,
                    'type' => $type,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Obtenir les statistiques d'interaction avec les autres modules
     */
    public function getStatistiquesInteractions()
    {
        return [
            'contrats_lies_financements' => JuridiqueContrat::whereHas('financementDossiers')->count(),
            'utilisateurs_actifs_juridique' => User::whereHas('juridiqueContrats')->distinct()->count(),
            'documents_par_contrat' => JuridiqueDocument::with('contrat')
                ->get()
                ->groupBy('contrat_id')
                ->map(fn($group) => $group->count()),
            'taux_conversion_financement' => $this->calculerTauxConversionFinancement(),
        ];
    }

    /**
     * Calculer le taux de conversion des dossiers de financement
     */
    private function calculerTauxConversionFinancement()
    {
        $totalDossiers = FinancementDossier::count();
        $dossiersAcceptes = FinancementDossier::where('statut', 'accepte')->count();
        
        return $totalDossiers > 0 ? ($dossiersAcceptes / $totalDossiers) * 100 : 0;
    }

    /**
     * Synchroniser les données avec le module RH
     */
    public function synchroniserAvecRH()
    {
        // Mettre à jour les informations des contrats liés aux employés
        $contratsEmploi = JuridiqueContrat::where('type_contrat', 'travail')->get();
        
        foreach ($contratsEmploi as $contrat) {
            // Logique de synchronisation avec les données RH
            if ($contrat->partie_contractante) {
                $employe = User::where('name', 'like', '%' . $contrat->partie_contractante . '%')->first();
                if ($employe) {
                    // Mettre à jour les informations si nécessaire
                    Log::info('Synchronisation contrat RH', [
                        'contrat_id' => $contrat->id,
                        'employe_id' => $employe->id,
                    ]);
                }
            }
        }
        
        return $contratsEmploi->count();
    }
}
