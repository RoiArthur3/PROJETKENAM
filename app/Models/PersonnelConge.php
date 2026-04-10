<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PersonnelConge extends Model
{
    use HasFactory;

    protected $table = 'personnel_conges';

    protected $fillable = [
        'personnel_id',
        'type',
        'date_debut',
        'date_fin',
        'nb_jours',
        'motif',
        'statut',
        'demande_par',
        'date_demande',
        'date_decision',
        'decision_par',
        'motif_decision',
        'date_annulation',
        'annule_par',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_demande' => 'datetime',
        'date_decision' => 'datetime',
        'date_annulation' => 'datetime',
    ];

    // Types de congés
    const TYPES = [
        'ANNUEL' => 'Congé annuel',
        'MALADIE' => 'Congé maladie',
        'MATERNITE' => 'Congé maternité',
        'PATERNITE' => 'Congé paternité',
        'EXCEPTIONNEL' => 'Congé exceptionnel',
    ];

    // Statuts possibles
    const STATUTS = [
        'EN_ATTENTE' => 'En attente de validation',
        'VALIDE' => 'Validé',
        'REFUSE' => 'Refusé',
        'ANNULE' => 'Annulé',
    ];

    // Relations
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function demandeur()
    {
        return $this->belongsTo(User::class, 'demande_par');
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'decision_par');
    }

    public function annulateur()
    {
        return $this->belongsTo(User::class, 'annule_par');
    }

    // Scopes
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'EN_ATTENTE');
    }

    public function scopeValides($query)
    {
        return $query->where('statut', 'VALIDE');
    }

    public function scopeRefuses($query)
    {
        return $query->where('statut', 'REFUSE');
    }

    public function scopeAnnules($query)
    {
        return $query->where('statut', 'ANNULE');
    }

    public function scopeEnCours($query)
    {
        return $query->valides()
            ->where('date_debut', '<=', now())
            ->where('date_fin', '>=', now());
    }

    public function scopeFuturs($query)
    {
        return $query->valides()->where('date_debut', '>', now());
    }

    public function scopePasse($query)
    {
        return $query->valides()->where('date_fin', '<', now());
    }

    public function scopeDePeriode($query, $debut, $fin)
    {
        return $query->where(function($q) use ($debut, $fin) {
            $q->whereBetween('date_debut', [$debut, $fin])
              ->orWhereBetween('date_fin', [$debut, $fin])
              ->orWhere(function($q2) use ($debut, $fin) {
                  $q2->where('date_debut', '<=', $debut)
                     ->where('date_fin', '>=', $fin);
              });
        });
    }

    // Accessors & Mutators
    public function getTypeLibelleAttribute()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatutLibelleAttribute()
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getDureeEnJoursAttribute()
    {
        return $this->nb_jours . ' jour' . ($this->nb_jours > 1 ? 's' : '');
    }

    public function getDateDebutFrAttribute()
    {
        return Carbon::parse($this->date_debut)->format('d/m/Y');
    }

    public function getDateFinFrAttribute()
    {
        return Carbon::parse($this->date_fin)->format('d/m/Y');
    }

    // Méthodes métier
    public function estEnAttente()
    {
        return $this->statut === 'EN_ATTENTE';
    }

    public function estValide()
    {
        return $this->statut === 'VALIDE';
    }

    public function estRefuse()
    {
        return $this->statut === 'REFUSE';
    }

    public function estAnnule()
    {
        return $this->statut === 'ANNULE';
    }

    public function estEnCours()
    {
        return $this->estValide() &&
               Carbon::parse($this->date_debut)->isPast() &&
               Carbon::parse($this->date_fin)->isFuture();
    }

    public function estFutur()
    {
        return $this->estValide() && Carbon::parse($this->date_debut)->isFuture();
    }

    public function estPasse()
    {
        return $this->estValide() && Carbon::parse($this->date_fin)->isPast();
    }

    public function peutEtreAnnule()
    {
        return $this->estEnAttente() || ($this->estValide() && $this->estFutur());
    }

    public function peutEtreValide()
    {
        return $this->estEnAttente();
    }

    public function peutEtreRefuse()
    {
        return $this->estEnAttente();
    }

    public function calculerJoursOuvres()
    {
        $debut = Carbon::parse($this->date_debut);
        $fin = Carbon::parse($this->date_fin);
        $jours = 0;

        while ($debut <= $fin) {
            // Exclure les weekends (samedi et dimanche)
            if (!$debut->isWeekend()) {
                $jours++;
            }
            $debut->addDay();
        }

        return $jours;
    }

    public function chevaucheAutreConge()
    {
        return PersonnelConge::where('personnel_id', $this->personnel_id)
            ->where('id', '!=', $this->id)
            ->valides()
            ->where(function($query) {
                $query->whereBetween('date_debut', [$this->date_debut, $this->date_fin])
                      ->orWhereBetween('date_fin', [$this->date_debut, $this->date_fin])
                      ->orWhere(function($q) {
                          $q->where('date_debut', '<=', $this->date_debut)
                            ->where('date_fin', '>=', $this->date_fin);
                      });
            })
            ->exists();
    }

    // Validation rules
    public static function getValidationRules()
    {
        return [
            'type' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'date_debut' => 'required|date|after_or_equal:today',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'motif' => 'nullable|string|max:500',
        ];
    }

    public static function getValidationMessages()
    {
        return [
            'type.required' => 'Le type de congé est obligatoire',
            'type.in' => 'Le type de congé sélectionné n\'est pas valide',
            'date_debut.required' => 'La date de début est obligatoire',
            'date_debut.after_or_equal' => 'La date de début ne peut pas être dans le passé',
            'date_fin.required' => 'La date de fin est obligatoire',
            'date_fin.after_or_equal' => 'La date de fin doit être postérieure à la date de début',
        ];
    }
}
