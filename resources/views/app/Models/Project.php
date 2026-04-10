<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'client_id',
        'user_id',
        'responsable_id',
        'nom',
        'type',
        'description',
        'budget_estime',
        'budget_reel',
        'date_debut',
        'date_fin_prevue',
        'date_fin_reelle',
        'pourcentage_avancement',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin_prevue' => 'date',
        'date_fin_reelle' => 'date',
    ];

    /**
     * Relations
     */
    
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function resources(): HasMany
    {
        return $this->hasMany(ProjectResource::class);
    }

    public function operations(): HasMany
    {
        return $this->hasMany(ProjectOperation::class);
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(ProjectExpense::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ProjectDocument::class);
    }

    public function milestones(): HasMany
    {
        return $this->hasMany(ProjectMilestone::class);
    }

    /**
     * Accesseurs et Mutateurs
     */

    public function getTotalDureeAttribute()
    {
        if ($this->date_debut && $this->date_fin_prevue) {
            return $this->date_debut->diffInDays($this->date_fin_prevue);
        }
        return null;
    }

    public function getRentabiliteAttribute()
    {
        // Rentabilité = revenus - dépenses
        $revenus = $this->getRevenus();
        $depenses = $this->budget_reel ?? 0;
        return $revenus - $depenses;
    }

    public function getTauxRentabiliteAttribute()
    {
        $revenus = $this->getRevenus();
        if ($revenus > 0) {
            return (($this->rentabilite) / $revenus) * 100;
        }
        return 0;
    }

    /**
     * Méthodes utiles
     */

    public function getDureeRetardAttribute()
    {
        if ($this->date_fin_reelle && $this->date_fin_prevue) {
            $retard = $this->date_fin_reelle->diffInDays($this->date_fin_prevue);
            return $retard > 0 ? $retard : 0;
        }
        return 0;
    }

    public function getDepassementBudgetAttribute()
    {
        if ($this->budget_estime && $this->budget_reel) {
            $depassement = $this->budget_reel - $this->budget_estime;
            return $depassement > 0 ? $depassement : 0;
        }
        return 0;
    }

    public function canValidate()
    {
        return $this->statut === 'brouillon';
    }

    public function canStart()
    {
        return $this->statut === 'valide';
    }

    public function canClose()
    {
        return $this->statut === 'en_cours';
    }

    public function getRevenus()
    {
        // Récupère les revenus liés aux opérations du projet
        // Ceci est une implémentation simple, à adapter selon votre logique
        return 0; // À implémenter avec les factures du projet
    }

    public function getTotalRessourcesAffectées()
    {
        return $this->resources()->where('statut', '!=', 'liberee')->count();
    }

    public function getOperationsCount()
    {
        return $this->operations()->count();
    }

    public function getExpensesTotal()
    {
        return $this->expenses()->sum('montant');
    }
}
