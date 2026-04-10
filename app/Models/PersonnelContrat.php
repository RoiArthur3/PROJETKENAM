<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class PersonnelContrat extends Model
{
    use HasFactory;

    protected $table = 'personnel_contrats';

    protected $fillable = [
        'personnel_id',
        'numero_contrat',
        'type_contrat',
        'date_debut',
        'date_fin',
        'duree_essai_jours',
        'fin_periode_essai',
        'poste',
        'description_taches',
        'obligations_employeur',
        'obligations_employe',
        'conditions_travail',
        'salaire_base',
        'devise',
        'frequence_paiement',
        'avantages',
        'lieu_travail',
        'service_affectation',
        'horaire_travail',
        'fichier_contrat_path',
        'fichier_annexe_path',
        'statut',
        'date_signature',
        'signe_par_id',
        'motif_resiliation',
        'date_resiliation',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'fin_periode_essai' => 'date',
        'date_signature' => 'date',
        'date_resiliation' => 'date',
        'salaire_base' => 'decimal:2',
        'duree_essai_jours' => 'integer',
    ];

    // Relations
    public function personnel(): BelongsTo
    {
        return $this->belongsTo(Personnel::class);
    }

    public function signePar(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signe_par_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessors & Mutators
    public function getDureeContratAttribute()
    {
        if (!$this->date_fin) return null;
        return $this->date_debut->diffInDays($this->date_fin);
    }

    public function getDureeEnMoisAttribute()
    {
        if (!$this->date_fin) return null;
        return $this->date_debut->diffInMonths($this->date_fin);
    }

    public function getSalaireBaseFormatteAttribute()
    {
        return number_format($this->salaire_base, 0, ',', ' ') . ' ' . $this->devise;
    }

    public function getEstEnPeriodeEssaiAttribute()
    {
        if (!$this->fin_periode_essai) return false;
        return now()->lte($this->fin_periode_essai) && $this->statut === 'ACTIF';
    }

    public function getContratBientotExpireAttribute()
    {
        if (!$this->date_fin) return false;
        return now()->addDays(30)->gte($this->date_fin) && $this->statut === 'ACTIF';
    }

    public function getJoursRestantsAttribute()
    {
        if (!$this->date_fin) return null;
        return now()->diffInDays($this->date_fin, false);
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('statut', 'ACTIF');
    }

    public function scopeDuPersonnel($query, $personnelId)
    {
        return $query->where('personnel_id', $personnelId);
    }

    public function scopeDuType($query, $type)
    {
        return $query->where('type_contrat', $type);
    }

    public function scopeExpirant($query, $jours = 30)
    {
        return $query->whereNotNull('date_fin')
                    ->where('date_fin', '<=', now()->addDays($jours))
                    ->where('statut', 'ACTIF');
    }

    public function scopeEnEssai($query)
    {
        return $query->whereNotNull('fin_periode_essai')
                    ->where('fin_periode_essai', '>=', now())
                    ->where('statut', 'ACTIF');
    }

    // Méthodes métier
    public function peutEtreResilie()
    {
        return in_array($this->statut, ['ACTIF', 'SIGNE']);
    }

    public function resilier($motif, $dateResiliation = null)
    {
        $this->motif_resiliation = $motif;
        $this->date_resiliation = $dateResiliation ?? now();
        $this->statut = 'RESILIE';
        $this->save();

        // Mettre à jour le statut du personnel si nécessaire
        if ($this->personnel) {
            $this->personnel->update([
                'statut' => 'DEPART',
                'date_depart' => $this->date_resiliation,
                'motif_depart' => 'Resiliation contrat: ' . $motif
            ]);
        }
    }

    public function signer(User $signataire)
    {
        $this->date_signature = now();
        $this->signe_par_id = $signataire->id;
        $this->statut = 'SIGNE';
        $this->save();

        // Activer le contrat si date début est atteinte
        if (now()->gte($this->date_debut)) {
            $this->statut = 'ACTIF';
            $this->save();
        }
    }

    public function genererNumeroContrat()
    {
        $prefix = match($this->type_contrat) {
            'CDI' => 'CDI',
            'CDD' => 'CDD',
            'STAGE' => 'STG',
            'APPRENTI' => 'APP',
            default => 'CTR'
        };

        $year = now()->year;
        $count = self::whereYear('created_at', $year)->count() + 1;
        
        $this->numero_contrat = $prefix . '-' . $year . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
        $this->save();
    }
}
