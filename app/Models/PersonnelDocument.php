<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class PersonnelDocument extends Model
{
    use HasFactory;

    protected $table = 'personnel_documents';

    protected $fillable = [
        'personnel_id',
        'nom_fichier',
        'chemin_fichier',
        'type_document',
        'taille_fichier',
        'date_expiration',
        'description',
        'statut',
        'upload_par',
        'date_validation',
        'valide_par',
        'motif_decision',
    ];

    protected $casts = [
        'date_expiration' => 'date',
        'date_validation' => 'datetime',
        'taille_fichier' => 'integer',
    ];

    // Types de documents
    const TYPES = [
        'CV' => 'CV',
        'CONTRAT' => 'Contrat de travail',
        'DIPLOME' => 'Diplôme',
        'ATTESTATION' => 'Attestation',
        'CASIER_JUDICIAIRE' => 'Casier judiciaire',
        'CERTIFICAT_MEDICAL' => 'Certificat médical',
        'PHOTO' => 'Photo d\'identité',
        'CNI' => 'Carte nationale d\'identité',
        'PASSEPORT' => 'Passeport',
        'PERMIS' => 'Permis de conduire',
        'AUTRE' => 'Autre',
    ];

    // Statuts possibles
    const STATUTS = [
        'EN_ATTENTE' => 'En attente de validation',
        'VALIDE' => 'Validé',
        'REJETE' => 'Rejeté',
        'EXPIRE' => 'Expiré',
    ];

    // Relations
    public function personnel()
    {
        return $this->belongsTo(Personnel::class);
    }

    public function uploadPar()
    {
        return $this->belongsTo(User::class, 'upload_par');
    }

    public function validePar()
    {
        return $this->belongsTo(User::class, 'valide_par');
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

    public function scopeRejetes($query)
    {
        return $query->where('statut', 'REJETE');
    }

    public function scopeExpires($query)
    {
        return $query->where('statut', 'EXPIRE');
    }

    public function scopeParType($query, $type)
    {
        return $query->where('type_document', $type);
    }

    public function scopeExpiresBientot($query, $jours = 30)
    {
        return $query->valides()
            ->whereNotNull('date_expiration')
            ->where('date_expiration', '<=', now()->addDays($jours))
            ->where('date_expiration', '>', now());
    }

    public function scopeExpirés($query)
    {
        return $query->valides()
            ->whereNotNull('date_expiration')
            ->where('date_expiration', '<', now());
    }

    public function scopeSansExpiration($query)
    {
        return $query->whereNull('date_expiration');
    }

    public function scopeRecherche($query, $terme)
    {
        return $query->where(function($q) use ($terme) {
            $q->where('nom_fichier', 'LIKE', "%{$terme}%")
              ->orWhere('description', 'LIKE', "%{$terme}%");
        });
    }

    // Accessors & Mutators
    public function getTypeLibelleAttribute()
    {
        return self::TYPES[$this->type_document] ?? $this->type_document;
    }

    public function getStatutLibelleAttribute()
    {
        return self::STATUTS[$this->statut] ?? $this->statut;
    }

    public function getDateExpirationFrAttribute()
    {
        return $this->date_expiration ? Carbon::parse($this->date_expiration)->format('d/m/Y') : null;
    }

    public function getDateValidationFrAttribute()
    {
        return $this->date_validation ? Carbon::parse($this->date_validation)->format('d/m/Y H:i') : null;
    }

    public function getExtensionFichierAttribute()
    {
        return pathinfo($this->nom_fichier, PATHINFO_EXTENSION);
    }

    public function getIconeTypeAttribute()
    {
        $extension = strtolower($this->extension_fichier);

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            return 'fa-file-image';
        } elseif (in_array($extension, ['pdf'])) {
            return 'fa-file-pdf';
        } elseif (in_array($extension, ['doc', 'docx'])) {
            return 'fa-file-word';
        } elseif (in_array($extension, ['xls', 'xlsx'])) {
            return 'fa-file-excel';
        } elseif (in_array($extension, ['zip', 'rar'])) {
            return 'fa-file-archive';
        } else {
            return 'fa-file';
        }
    }

    public function getCouleurStatutAttribute()
    {
        switch ($this->statut) {
            case 'EN_ATTENTE':
                return 'warning';
            case 'VALIDE':
                return 'success';
            case 'REJETE':
                return 'danger';
            case 'EXPIRE':
                return 'secondary';
            default:
                return 'light';
        }
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

    public function estRejete()
    {
        return $this->statut === 'REJETE';
    }

    public function estExpire()
    {
        return $this->statut === 'EXPIRE';
    }

    public function peutEtreValide()
    {
        return $this->estEnAttente();
    }

    public function peutEtreRejete()
    {
        return $this->estEnAttente();
    }

    public function peutEtreSupprime()
    {
        return !$this->estValide() || $this->estExpire();
    }

    public function estExpireBientot($jours = 30)
    {
        return $this->estValide() &&
               $this->date_expiration &&
               Carbon::parse($this->date_expiration)->diffInDays(now()) <= $jours;
    }

    public function estPerime()
    {
        return $this->estValide() &&
               $this->date_expiration &&
               Carbon::parse($this->date_expiration)->isPast();
    }

    public function verifierExpiration()
    {
        if ($this->estValide() && $this->estPerime()) {
            $this->update(['statut' => 'EXPIRE']);
            return true;
        }
        return false;
    }

    public function marquerCommeValide($userId = null, $motif = null)
    {
        $this->update([
            'statut' => 'VALIDE',
            'date_validation' => now(),
            'valide_par' => $userId ?? Auth::id(),
            'motif_decision' => $motif,
        ]);
    }

    public function marquerCommeRejete($userId = null, $motif = null)
    {
        $this->update([
            'statut' => 'REJETE',
            'date_validation' => now(),
            'valide_par' => $userId ?? Auth::id(),
            'motif_decision' => $motif,
        ]);
    }

    public function getUrlAttribute()
    {
        try {
            // Utiliser asset() qui est mieux reconnu par les IDE
            $path = $this->chemin_fichier;
            if (Storage::disk('public')->exists($path)) {
                return asset('storage/' . $path);
            }
            return null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getCheminCompletAttribute()
    {
        try {
            return Storage::disk('public')->path($this->chemin_fichier);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function existePhysiquement()
    {
        try {
            return Storage::disk('public')->exists($this->chemin_fichier);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function supprimerFichierPhysique()
    {
        try {
            if ($this->existePhysiquement()) {
                return Storage::disk('public')->delete($this->chemin_fichier);
            }
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    // Vérifications de sécurité
    public function estAutorisePourUtilisateur($userId)
    {
        return $this->personnel->user_id === $userId ||
               $this->upload_par === $userId ||
               (Auth::check() && $this->utilisateurARoleAdmin());
    }

    private function utilisateurARoleAdmin()
    {
        $user = Auth::user();
        if (!$user) {
            return false;
        }

        // Vérifier le rôle selon la structure de votre application
        // Adaptez cette logique selon votre système de rôles
        return in_array($user->role ?? '', ['admin', 'superadmin', 'rh']) ||
               (isset($user->is_admin) && $user->is_admin) ||
               $this->utilisateurARoleAdmin($user);
    }

    public function estDeTypeOfficiel()
    {
        $typesOfficiels = ['CNI', 'PASSEPORT', 'PERMIS', 'CASIER_JUDICIAIRE', 'CERTIFICAT_MEDICAL'];
        return in_array($this->type_document, $typesOfficiels);
    }

    public function estDeTypeFormation()
    {
        $typesFormation = ['DIPLOME', 'ATTESTATION', 'CV'];
        return in_array($this->type_document, $typesFormation);
    }

    // Validation rules
    public static function getValidationRules()
    {
        return [
            'nom_fichier' => 'required|string|max:255',
            'chemin_fichier' => 'required|string|max:500',
            'type_document' => 'required|in:' . implode(',', array_keys(self::TYPES)),
            'taille_fichier' => 'nullable|integer|min:0',
            'date_expiration' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:500',
            'statut' => 'required|in:' . implode(',', array_keys(self::STATUTS)),
        ];
    }

    public static function getValidationMessages()
    {
        return [
            'nom_fichier.required' => 'Le nom du fichier est obligatoire',
            'type_document.required' => 'Le type de document est obligatoire',
            'type_document.in' => 'Le type de document sélectionné n\'est pas valide',
            'date_expiration.after' => 'La date d\'expiration doit être dans le futur',
            'statut.required' => 'Le statut est obligatoire',
            'statut.in' => 'Le statut sélectionné n\'est pas valide',
        ];
    }

    // Statistiques
    public static function getStatistiquesParType($personnelId = null)
    {
        $query = self::query();

        if ($personnelId) {
            $query->where('personnel_id', $personnelId);
        }

        return $query->selectRaw('type_document, COUNT(*) as nb, SUM(taille_fichier) as taille_totale')
            ->groupBy('type_document')
            ->orderBy('nb', 'desc')
            ->get()
            ->map(function($stat) {
                return [
                    'type' => $stat->type_document,
                    'libelle' => self::TYPES[$stat->type_document] ?? $stat->type_document,
                    'nombre' => $stat->nb,
                    'taille_totale' => self::formatFileSize($stat->taille_totale),
                ];
            });
    }

    public static function getDocumentsExpiresBientot($jours = 30, $personnelId = null)
    {
        $query = self::expiresBientot($jours);

        if ($personnelId) {
            $query->where('personnel_id', $personnelId);
        }

        return $query->with('personnel')->get();
    }

    // Utilitaires
    private static function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
