<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\PersonnelConge;
use App\Models\PersonnelPaie;
use App\Models\PersonnelDocument;

class Personnel extends Model
{
    use HasFactory;

    protected $table = 'personnel'; // Pour correspondre à la table créée par la migration

    protected $fillable = [
        'matricule',
        'nom',
        'prenoms',
        'date_naissance',
        'lieu_naissance',
        'nationalite',
        'sexe',
        'situation_matrimoniale',
        'nb_enfants_charge',
        'telephone_principal',
        'telephone_secondaire',
        'email_personnel',
        'adresse_residence',
        'ville',
        'quartier',
        'type_piece',
        'numero_piece',
        'date_delivrance_piece',
        'expiration_piece',
        'lieu_delivrance_piece',
        'poste',
        'service',
        'departement',
        'categorie',
        'echelon',
        'indice',
        'user_id',
        'type_contrat',
        'date_embauche',
        'date_fin_contrat',
        'duree_essai_jours',
        'fin_periode_essai',
        'salaire_base',
        'devise',
        'mode_paiement',
        'frequence_paiement',
        'numero_cnps',
        'date_affiliation_cnps',
        'categorie_cnps',
        'numero_contribuable',
        'nb_parts_fiscales',
        'situation_fiscale',
        'banque',
        'agence_bancaire',
        'numero_compte_bancaire',
        'rib',
        'nom_urgence',
        'telephone_urgence',
        'lien_parente',
        'adresse_urgence',
        'groupe_sanguin',
        'allergies',
        'maladies_chroniques',
        'medecin_traitant',
        'telephone_medecin',
        'photo_profil',
        'cv_path',
        'lettre_motivation_path',
        'contrat_path',
        'casier_judiciaire_path',
        'certificat_medical_path',
        'diplomes_path',
        'attestations_path',
        'statut',
        'date_depart',
        'motif_depart',
        'observations',
        'created_by',
        'updated_by',
        // Champs pour synchronisation faciale
        'camera_person_id',
        'facial_sync_status', // 'pending', 'synced', 'failed', 'removed'
        'facial_sync_at',
        'facial_device_id',
    ];

    protected $casts = [
        'date_naissance' => 'date',
        'date_delivrance_piece' => 'date',
        'expiration_piece' => 'date',
        'date_embauche' => 'date',
        'date_fin_contrat' => 'date',
        'fin_periode_essai' => 'date',
        'date_affiliation_cnps' => 'date',
        'date_depart' => 'date',
        'salaire_base' => 'decimal:2',
        'nb_enfants_charge' => 'integer',
        'duree_essai_jours' => 'integer',
        'nb_parts_fiscales' => 'integer',
        'camera_person_id' => 'string',
        'facial_sync_at' => 'datetime',
        'facial_device_id' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    // Relations
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Relations pour les fonctionnalités spécifiques
    public function conges()
    {
        return $this->hasMany(PersonnelConge::class);
    }

    public function paies()
    {
        return $this->hasMany(PersonnelPaie::class);
    }

    public function documents()
    {
        return $this->hasMany(PersonnelDocument::class);
    }

    public function getNumeroCompteBancaireAttribute()
    {
        return $this->attributes['numero_compte'] ?? null;
    }

    public function setNumeroCompteBancaireAttribute($value): void
    {
        $this->attributes['numero_compte'] = $value;
    }

    public function getLienParenteAttribute()
    {
        return $this->attributes['lien_urgence'] ?? null;
    }

    public function setLienParenteAttribute($value): void
    {
        $this->attributes['lien_urgence'] = $value;
    }

    public function contrats()
    {
        return $this->hasMany(PersonnelContrat::class);
    }

    public function contratActif()
    {
        return $this->hasOne(PersonnelContrat::class)->where('statut', 'ACTIF');
    }

    // Relations pour les conges actifs et validés
    public function congesValides()
    {
        return $this->conges()->valides();
    }

    public function congesEnAttente()
    {
        return $this->conges()->enAttente();
    }

    public function congesEnCours()
    {
        return $this->conges()->enCours();
    }

    // Relations pour les paies par statut
    public function paiesPayees()
    {
        return $this->paies()->payes();
    }

    public function paiesValides()
    {
        return $this->paies()->valides();
    }

    public function paiesEnAttente()
    {
        return $this->paies()->enAttente();
    }

    // Relations pour les documents par statut
    public function documentsValides()
    {
        return $this->documents()->valides();
    }

    public function documentsEnAttente()
    {
        return $this->documents()->enAttente();
    }

    public function documentsExpires()
    {
        return $this->documents()->expires();
    }

    // Accessors & Mutators
    public function getNomCompletAttribute()
    {
        return $this->nom . ' ' . $this->prenoms;
    }

    public function getAgeAttribute()
    {
        return $this->date_naissance ? $this->date_naissance->age : null;
    }

    public function getAncienneteAttribute()
    {
        return $this->date_embauche ? $this->date_embauche->diffInYears(now()) : 0;
    }

    public function getSalaireBaseFormatteAttribute()
    {
        return number_format($this->salaire_base, 0, ',', ' ') . ' ' . $this->devise;
    }

    public function getEnEssaiAttribute()
    {
        if (!$this->fin_periode_essai) return false;
        return now()->lte($this->fin_periode_essai) && $this->statut === 'ACTIF';
    }

    public function getContratBientotExpireAttribute()
    {
        if (!$this->date_fin_contrat) return false;
        return now()->addDays(30)->gte($this->date_fin_contrat) && $this->statut === 'ACTIF';
    }

    // Scopes
    public function scopeActif($query)
    {
        return $query->where('statut', 'ACTIF');
    }

    public function scopeDuService($query, $service)
    {
        return $query->where('service', $service);
    }

    public function scopeDuPoste($query, $poste)
    {
        return $query->where('poste', $poste);
    }

    public function scopeEnEssai($query)
    {
        return $query->whereNotNull('fin_periode_essai')
                    ->where('fin_periode_essai', '>=', now())
                    ->where('statut', 'ACTIF');
    }

    public function scopeContratExpirant($query, $jours = 30)
    {
           $jours = (int) $jours;

        return $query->whereNotNull('date_fin_contrat')
                    ->where('date_fin_contrat', '<=', now()->addDays($jours))
                    ->where('statut', 'ACTIF');
    }

    // Méthodes métier
    public function peutPrendreConge()
    {
        return $this->statut === 'ACTIF' && !$this->enEssai;
    }

    public function estEnPeriodeEssai()
    {
        return $this->enEssai;
    }

    public function joursAnciennete()
    {
        return $this->date_embauche ? $this->date_embauche->diffInDays(now()) : 0;
    }

    public function soldeCongeAnnuel()
    {
        // Calcul selon la législation ivoirienne : 2.5 jours par mois d'ancienneté
        return floor($this->anciennete * 2.5);
    }
}
