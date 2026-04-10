<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EvaluationFournisseur extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'reference',
        'fournisseur_id',
        'date_evaluation',
        'evaluateur_id',
        'periode_debut',
        'periode_fin',
        'note_globale',
        'note_qualite',
        'note_prix',
        'note_delai',
        'note_service',
        'note_reactivite',
        'note_globale_ponderee',
        'commentaires',
        'points_forts',
        'points_faibles',
        'preconisations',
        'statut',
        'est_anonyme',
        'criteres',
        'user_id'
    ];

    protected $casts = [
        'date_evaluation' => 'date',
        'periode_debut' => 'date',
        'periode_fin' => 'date',
        'note_globale' => 'float',
        'note_qualite' => 'float',
        'note_prix' => 'float',
        'note_delai' => 'float',
        'note_service' => 'float',
        'note_reactivite' => 'float',
        'note_globale_ponderee' => 'float',
        'points_forts' => 'array',
        'points_faibles' => 'array',
        'preconisations' => 'array',
        'criteres' => 'array',
        'est_anonyme' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($evaluation) {
            if (empty($evaluation->reference)) {
                $evaluation->reference = 'EVAL-' . date('Ymd') . '-' . strtoupper(Str::random(6));
            }
            
            if (empty($evaluation->date_evaluation)) {
                $evaluation->date_evaluation = now();
            }
            
            if (empty($evaluation->statut)) {
                $evaluation->statut = 'brouillon';
            }
            
            $evaluation->calculerNotes();
        });

        static::saving(function ($evaluation) {
            $evaluation->calculerNotes();
        });

        static::saved(function ($evaluation) {
            // Mettre à jour la note moyenne du fournisseur
            if ($evaluation->fournisseur) {
                $evaluation->fournisseur->evaluation_moyenne = $evaluation->fournisseur->evaluations()
                    ->where('statut', 'termine')
                    ->avg('note_globale_ponderee');
                $evaluation->fournisseur->save();
            }
        });
    }

    public function calculerNotes()
    {
        // Calcul des notes moyennes si non définies
        if (empty($this->note_qualite) && !empty($this->criteres['qualite'])) {
            $this->note_qualite = collect($this->criteres['qualite'])->avg('note');
        }
        
        if (empty($this->note_prix) && !empty($this->criteres['prix'])) {
            $this->note_prix = collect($this->criteres['prix'])->avg('note');
        }
        
        if (empty($this->note_delai) && !empty($this->criteres['delai'])) {
            $this->note_delai = collect($this->criteres['delai'])->avg('note');
        }
        
        if (empty($this->note_service) && !empty($this->criteres['service'])) {
            $this->note_service = collect($this->criteres['service'])->avg('note');
        }
        
        if (empty($this->note_reactivite) && !empty($this->criteres['reactivite'])) {
            $this->note_reactivite = collect($this->criteres['reactivite'])->avg('note');
        }
        
        // Calcul de la note globale (moyenne simple des notes principales)
        $notes = array_filter([
            $this->note_qualite,
            $this->note_prix,
            $this->note_delai,
            $this->note_service,
            $this->note_reactivite
        ], function($note) {
            return !is_null($note);
        });
        
        if (!empty($notes)) {
            $this->note_globale = round(array_sum($notes) / count($notes), 2);
        }
        
        // Calcul de la note globale pondérée (avec coefficients personnalisables)
        $poids = [
            'qualite' => 0.30,
            'prix' => 0.25,
            'delai' => 0.20,
            'service' => 0.15,
            'reactivite' => 0.10
        ];
        
        $totalPoids = 0;
        $totalPondere = 0;
        
        foreach ($poids as $critere => $poidsCritere) {
            $note = $this->{'note_' . $critere};
            if (!is_null($note)) {
                $totalPondere += $note * $poidsCritere;
                $totalPoids += $poidsCritere;
            }
        }
        
        if ($totalPoids > 0) {
            $this->note_globale_ponderee = round($totalPondere / $totalPoids, 2);
        }
    }

    public function fournisseur()
    {
        return $this->belongsTo(Fournisseur::class);
    }

    public function evaluateur()
    {
        return $this->belongsTo(User::class, 'evaluateur_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function commandes()
    {
        return $this->belongsToMany(CommandeFournisseur::class, 'evaluation_commande', 'evaluation_id', 'commande_id')
            ->withTimestamps();
    }

    public function getStatutBadgeAttribute()
    {
        $badges = [
            'brouillon' => 'secondary',
            'en_cours' => 'primary',
            'termine' => 'success',
            'annule' => 'danger'
        ];

        $statut = $this->statut;
        $libelle = ucfirst($statut);
        
        return sprintf('<span class="badge badge-%s">%s</span>', 
            $badges[$statut] ?? 'secondary', 
            $libelle
        );
    }

    public function getNoteGlobaleFormateeAttribute()
    {
        return number_format($this->note_globale_ponderee ?? $this->note_globale, 1, ',', ' ');
    }

    public function getNoteQualiteFormateeAttribute()
    {
        return $this->note_qualite ? number_format($this->note_qualite, 1, ',', ' ') : 'N/A';
    }

    public function getNotePrixFormateeAttribute()
    {
        return $this->note_prix ? number_format($this->note_prix, 1, ',', ' ') : 'N/A';
    }

    public function getNoteDelaiFormateeAttribute()
    {
        return $this->note_delai ? number_format($this->note_delai, 1, ',', ' ') : 'N/A';
    }

    public function getNoteServiceFormateeAttribute()
    {
        return $this->note_service ? number_format($this->note_service, 1, ',', ' ') : 'N/A';
    }

    public function getNoteReactiviteFormateeAttribute()
    {
        return $this->note_reactivite ? number_format($this->note_reactivite, 1, ',', ' ') : 'N/A';
    }

    public function getPeriodeAttribute()
    {
        if ($this->periode_debut && $this->periode_fin) {
            return $this->periode_debut->format('d/m/Y') . ' - ' . $this->periode_fin->format('d/m/Y');
        }
        
        if ($this->periode_debut) {
            return 'À partir du ' . $this->periode_debut->format('d/m/Y');
        }
        
        if ($this->periode_fin) {
            return 'Jusqu\'au ' . $this->periode_fin->format('d/m/Y');
        }
        
        return 'Non spécifiée';
    }

    public function getEstTermineeAttribute()
    {
        return $this->statut === 'termine';
    }

    public function getEstBrouillonAttribute()
    {
        return $this->statut === 'brouillon';
    }

    public function getEstEnCoursAttribute()
    {
        return $this->statut === 'en_cours';
    }
}
