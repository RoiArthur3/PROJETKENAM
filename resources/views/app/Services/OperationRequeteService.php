<?php

namespace App\Services;

use App\Models\Operation;
use App\Models\OperationHistorique;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class OperationRequeteService
{
    public const STATUTS = [
        'ENREGISTREE' => 'Enregistrée',
        'EN_ATTENTE_ENVOI' => 'En attente d\'envoi',
        'ENVOYEE' => 'Envoyée',
        'EN_COURS_DE_TRAITEMENT' => 'En cours de traitement',
        'TRANSFERE' => 'Transférée',
        'CLOTUREE' => 'Clôturée',
        'REJETEE' => 'Rejetée',
    ];

    public const PRIORITES = [
        'BASSE' => 'Basse',
        'MOYENNE' => 'Moyenne',
        'HAUTE' => 'Haute',
        'URGENTE' => 'Urgente',
    ];

    public function creerRequete(array $data): Operation
    {
        return DB::transaction(function () use ($data) {
            $operation = new Operation();
            $operation->nom = $data['nom'];
            $operation->description = $data['description'];
            $operation->service_emetteur_id = $data['service_emetteur_id'] ?? auth()->user()->service_id;
            $operation->service_destinataire_id = $data['service_destinataire_id'];
            $operation->destinataire_id = $data['destinataire_id'] ?? null;
            $operation->user_id = $data['user_id'] ?? auth()->id();
            $operation->statut_requete = 'ENREGISTREE';
            $operation->type_requete = $data['type_requete'];
            $operation->priorite = $data['priorite'] ?? 'MOYENNE';
            $operation->reference_requete = $this->generateReference();
            $operation->save();

            $this->ajouterHistorique($operation, 'CREATION', 'Requête créée');

            return $operation;
        });
    }

    public function changerStatut(Operation $operation, string $nouveauStatut, ?string $commentaire = null, ?User $user = null): void
    {
        DB::transaction(function () use ($operation, $nouveauStatut, $commentaire, $user) {
            $ancienStatut = $operation->statut_requete;
            $operation->statut_requete = $nouveauStatut;

            $dates = [
                'ENVOYEE' => 'date_envoi',
                'CLOTUREE' => 'date_cloture',
                'REJETEE' => 'date_cloture',
            ];

            if (isset($dates[$nouveauStatut])) {
                $operation->{$dates[$nouveauStatut]} = now();
            }

            $operation->save();

            $this->ajouterHistorique($operation, 'CHANGEMENT_STATUT', $commentaire, $user, $ancienStatut, $nouveauStatut);

            // Déclencher l'événement approprié
            if ($nouveauStatut === 'ENVOYEE') {
                $this->envoyerNotification($operation);
            }
        });
    }

    public function envoyerRequete(Operation $operation, ?string $commentaire = null): void
    {
        $this->changerStatut($operation, 'ENVOYEE', $commentaire);
    }

    public function cloturerRequete(Operation $operation, string $commentaire, ?User $user = null): void
    {
        $this->changerStatut($operation, 'CLOTUREE', $commentaire, $user);
    }

    public function transfererRequete(Operation $operation, int $nouveauServiceId, ?int $nouveauDestinataireId, string $commentaire): void
    {
        DB::transaction(function () use ($operation, $nouveauServiceId, $nouveauDestinataireId, $commentaire) {
            $ancienService = $operation->serviceDestinataire->nom;
            $nouveauService = Service::find($nouveauServiceId)->nom;

            $operation->service_destinataire_id = $nouveauServiceId;
            $operation->destinataire_id = $nouveauDestinataireId;
            $operation->statut_requete = 'TRANSFERE';
            $operation->save();

            $this->ajouterHistorique($operation, 'TRANSFERT', 
                "Transférée de {$ancienService} vers {$nouveauService}. {$commentaire}");

            $this->envoyerNotification($operation, 'transfert');
        });
    }

    public function ajouterCommentaire(Operation $operation, string $commentaire, ?User $user = null): void
    {
        $this->ajouterHistorique($operation, 'COMMENTAIRE', $commentaire, $user);
    }

    public function ajouterHistorique(Operation $operation, string $action, ?string $commentaire = null, ?User $user = null, ?string $ancienStatut = null, ?string $nouveauStatut = null): void
    {
        $operation->historiques()->create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'ancien_statut' => $ancienStatut,
            'nouveau_statut' => $nouveauStatut,
            'commentaire' => $commentaire,
        ]);
    }

    public function getStatistiques(?int $serviceId = null): array
    {
        $query = Operation::query();

        if ($serviceId) {
            $query->where('service_destinataire_id', $serviceId);
        }

        return [
            'total' => $query->count(),
            'en_attente' => $query->where('statut_requete', 'ENVOYEE')->count(),
            'en_cours' => $query->where('statut_requete', 'EN_COURS_DE_TRAITEMENT')->count(),
            'cloturees' => $query->where('statut_requete', 'CLOTUREE')->count(),
            'rejetees' => $query->where('statut_requete', 'REJETEE')->count(),
            'par_service' => $query->select('service_destinataire_id')
                ->selectRaw('count(*) as total')
                ->groupBy('service_destinataire_id')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [$item->service_destinataire_id => $item->total];
                }),
        ];
    }

    public function getOperationsParService(int $serviceId, string $type = 'recues')
    {
        $query = Operation::query();

        if ($type === 'recues') {
            $query->where('service_destinataire_id', $serviceId);
        } else {
            $query->where('service_emetteur_id', $serviceId);
        }

        return $query->with(['serviceEmetteur', 'serviceDestinataire', 'user'])
            ->latest('created_at');
    }

    public function getOperationsEnAttente(int $serviceId): 
    {
        return $this->getOperationsParService($serviceId, 'recues')
            ->where('statut_requete', 'ENVOYEE');
    }

    public function getOperationsEnCours(int $serviceId): 
    {
        return $this->getOperationsParService($serviceId, 'recues')
            ->where('statut_requete', 'EN_COURS_DE_TRAITEMENT');
    }

    public function getHistorique(Operation $operation): 
    {
        return $operation->historiques()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    private function generateReference(): string
    {
        $prefix = 'REQ';
        $date = now()->format('Ymd');
        $count = Operation::whereDate('created_at', now())->count() + 1;
        
        return $prefix . $date . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    private function envoyerNotification(Operation $operation, string $type = 'nouvelle'): void
    {
        $destinataire = $operation->destinataire ?? 
                       User::where('service_id', $operation->service_destinataire_id)->first();

        if (!$destinataire || !$destinataire->email) {
            return;
        }

        $data = [
            'operation' => $operation,
            'emetteur' => auth()->user(),
            'serviceEmetteur' => $operation->serviceEmetteur,
            'serviceDestinataire' => $operation->serviceDestinataire,
        ];

        try {
            Mail::to($destinataire->email)
                ->send(new \App\Mail\OperationNotification($data, $type));
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas le processus
            \Log::error('Erreur envoi email notification: ' . $e->getMessage());
        }
    }
}
