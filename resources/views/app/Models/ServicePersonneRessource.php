<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePersonneRessource extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'user_id',
        'role',
        'recevoir_emails',
    ];

    protected $casts = [
        'recevoir_emails' => 'boolean',
    ];

    /**
     * Obtenir le service associé
     */
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Obtenir l'utilisateur associé
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir les emails des personnes ressources pour un service
     */
    public static function getEmailsForService($serviceId)
    {
        return self::where('service_id', $serviceId)
            ->where('recevoir_emails', true)
            ->with('user')
            ->get()
            ->map(function ($personne) {
                return $personne->user->email;
            })
            ->filter()
            ->toArray();
    }

    /**
     * Vérifier si un utilisateur est personne ressource pour un service
     */
    public static function isPersonneRessource($userId, $serviceId)
    {
        return self::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->exists();
    }

    /**
     * Obtenir les services pour lesquels un utilisateur est personne ressource
     */
    public static function getServicesForUser($userId)
    {
        return self::where('user_id', $userId)
            ->with('service')
            ->get()
            ->map(function ($personne) {
                return $personne->service;
            });
    }
}
