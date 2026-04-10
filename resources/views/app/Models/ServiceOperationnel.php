<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class ServiceOperationnel extends Model
{
    use HasFactory, Notifiable;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'services_operationnels';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'nom',
        'code',
        'description',
        'email',
        'password',
        'telephone',
        'responsable',
        'couleur',
        'icone',
        'actif',
        'ordre',
    ];

    /**
     * Get the user that is the responsable for the service.
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'actif' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the users associated with this service.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'service_user')
                    ->withPivot('role', 'actif')
                    ->withTimestamps();
    }

    /**
     * Get the requests assigned to this service.
     */
    public function requetes()
    {
        return $this->hasMany(Requete::class, 'service_destinataire_id')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Get the requests created by this service.
     */
    public function requetesEmises()
    {
        return $this->hasMany(Requete::class, 'service_emetteur_id')
                    ->orderBy('created_at', 'desc');
    }

    /**
     * Scope to get only active services.
     */
    public function scopeActif($query)
    {
        return $query->where('actif', true);
    }

    /**
     * Scope to order by order field.
     */
    public function scopeOrdre($query)
    {
        return $query->orderBy('ordre', 'asc');
    }

    /**
     * Get the full email address for notifications.
     */
    public function getRouteNotificationForMail($notification = null)
    {
        return $this->email;
    }

    /**
     * Get the display name with code.
     */
    public function getDisplayNameAttribute()
    {
        return "{$this->code} - {$this->nom}";
    }

    /**
     * Get the service icon with color.
     */
    public function getIconHtmlAttribute()
    {
        return "<i class='{$this->icone}' style='color: {$this->couleur};'></i>";
    }

    /**
     * Get the service badge HTML.
     */
    public function getBadgeHtmlAttribute()
    {
        $status = $this->actif ? 'success' : 'secondary';
        $text = $this->actif ? 'Actif' : 'Inactif';

        return "<span class='badge badge-{$status}'>{$text}</span>";
    }

    /**
     * Check if service can receive requests.
     */
    public function peutRecevoirRequetes(): bool
    {
        return $this->actif && !empty($this->email);
    }

    /**
     * Get service statistics.
     */
    public function getStatistiques()
    {
        return [
            'total_requetes' => $this->requetes()->count(),
            'requetes_en_cours' => $this->requetes()->where('statut', 'en_cours')->count(),
            'requetes_terminees' => $this->requetes()->where('statut', 'termine')->count(),
            'utilisateurs_actifs' => $this->users()->wherePivot('actif', true)->count(),
        ];
    }

    /**
     * Send notification to all service users.
     */
    public function notifierService($notification)
    {
        $users = $this->users()->wherePivot('actif', true)->get();

        foreach ($users as $user) {
            $user->notify($notification);
        }
    }

    /**
     * Get the primary contact information.
     */
    public function getContactPrincipal()
    {
        return [
            'email' => $this->email,
            'telephone' => $this->telephone,
            'responsable' => $this->responsable,
            'service' => $this->nom,
        ];
    }

    /**
     * Check if email configuration is valid.
     */
    public function hasValidEmailConfig(): bool
    {
        return !empty($this->email) &&
               filter_var($this->email, FILTER_VALIDATE_EMAIL) &&
               !empty($this->password);
    }

    /**
     * Get the service configuration for mail sending.
     */
    public function getMailConfig()
    {
        return [
            'mailer' => 'smtp',
            'host' => 'folina.o2switch.net',
            'port' => 465,
            'encryption' => 'ssl',
            'username' => $this->email,
            'password' => $this->password,
            'from' => [
                'address' => 'noreply@kenamservices.com',
                'name' => "KENAM SERVICES - {$this->nom}",
            ],
        ];
    }
}
