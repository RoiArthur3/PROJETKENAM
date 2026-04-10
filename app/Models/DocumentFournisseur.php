<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DocumentFournisseur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'fournisseur_id',
        'type_document',
        'categorie',
        'nom_fichier',
        'chemin_fichier',
        'date_emission',
        'date_expiration',
        'date_notification',
        'statut',
        'est_obligatoire',
        'est_verifie',
        'date_verification',
        'verificateur_id',
        'notes',
        'metadata',
        'user_id'
    ];

    protected $casts = [
        'date_emission' => 'date',
        'date_expiration' => 'date',
        'date_notification' => 'date',
        'date_verification' => 'datetime',
        'est_obligatoire' => 'boolean',
        'est_verifie' => 'boolean',
        'notes' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($document) {
            if (empty($document->reference)) {
                $document->reference = 'DOC-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            
            if (empty($document->statut)) {
                $document->statut = 'actif';
            }
            
            if ($document->date_emission && !$document->date_notification && $document->date_expiration) {
                // Définir une date de notification par défaut (30 jours avant l'expiration)
                $dateNotification = Carbon::parse($document->date_expiration)->subDays(30);
                if ($dateNotification->gt(now())) {
                    $document->date_notification = $dateNotification;
                }
            }
        });

        static::saving(function ($document) {
            // Mettre à jour le statut du document en fonction des dates
            if ($document->date_expiration) {
                $now = now();
                $expiration = Carbon::parse($document->date_expiration);
                
                if ($expiration->isPast()) {
                    $document->statut = 'expire';
                } elseif ($document->date_notification && $now->gte(Carbon::parse($document->date_notification))) {
                    $document->statut = 'a_renouveler';
                } else {
                    $document->statut = 'valide';
                }
            }
        });
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function verificateur()
    {
        return $this->belongsTo(User::class, 'verificateur_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatutBadgeAttribute()
    {
        $badges = [
            'brouillon' => 'secondary',
            'valide' => 'success',
            'a_renouveler' => 'warning',
            'expire' => 'danger',
            'rejete' => 'danger',
            'en_attente' => 'info',
            'archive' => 'dark'
        ];

        $statut = $this->statut;
        $libelle = ucfirst(str_replace('_', ' ', $statut));
        
        return sprintf('<span class="badge badge-%s">%s</span>', 
            $badges[$statut] ?? 'secondary', 
            $libelle
        );
    }

    public function getEstExpireAttribute()
    {
        return $this->date_expiration && Carbon::parse($this->date_expiration)->isPast();
    }

    public function getJoursAvantExpirationAttribute()
    {
        if (!$this->date_expiration) return null;
        
        return Carbon::now()->diffInDays(Carbon::parse($this->date_expiration), false);
    }

    public function getEstBientotExpireAttribute()
    {
        if (!$this->date_expiration) return false;
        
        $joursRestants = $this->jours_avant_expiration;
        return $joursRestants > 0 && $joursRestants <= 30;
    }

    public function getExtensionFichierAttribute()
    {
        return pathinfo($this->nom_fichier, PATHINFO_EXTENSION);
    }

    public function getTailleFichierFormateeAttribute()
    {
        if (!$this->metadata || !isset($this->metadata['taille'])) {
            return 'N/A';
        }

        $bytes = $this->metadata['taille'];
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function getEstVerifiableAttribute()
    {
        return in_array($this->type_document, [
            'certificat', 'attestation', 'assurance', 'kbis', 'rib', 'carte_identite',
            'permis_conduire', 'carte_grise', 'certificat_immatriculation'
        ]);
    }

    public function getEstRenouvelableAttribute()
    {
        return in_array($this->type_document, [
            'assurance', 'visite_technique', 'contrat', 'autorisation', 'certificat', 'attestation'
        ]);
    }

    public function getIconeTypeAttribute()
    {
        $icones = [
            'facture' => 'fa-file-invoice-dollar',
            'devis' => 'fa-file-invoice',
            'bon_commande' => 'fa-file-purchase',
            'bon_livraison' => 'fa-truck-loading',
            'contrat' => 'fa-file-contract',
            'kbis' => 'fa-building',
            'rib' => 'fa-university',
            'assurance' => 'fa-shield-alt',
            'certificat' => 'fa-certificate',
            'attestation' => 'fa-file-certificate',
            'carte_identite' => 'fa-id-card',
            'permis_conduire' => 'fa-id-card',
            'carte_grise' => 'fa-file-alt',
            'certificat_immatriculation' => 'fa-file-alt',
            'visite_technique' => 'fa-clipboard-check',
            'autorisation' => 'fa-file-signature',
            'autre' => 'fa-file'
        ];
        
        return $icones[$this->type_document] ?? 'fa-file';
    }
}
