<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntrepriseSettings extends Model
{
    protected $fillable = [
        'nom_entreprise',
        'sigle',
        'adresse',
        'telephone',
        'email_contact',
        'site_web',
        'rccm',
        'compte_bancaire',
        'logo_path',
        'ifu',
        'cnss',
        'email_noreply',
        'email_support',
        'telephone_support',
        'actif',
    ];

    protected $casts = [
        'actif' => 'boolean',
    ];

    /**
     * Obtenir les paramètres de l'entreprise actifs
     */
    public static function getActive()
    {
        return self::where('actif', true)->first();
    }

    /**
     * Obtenir une valeur spécifique des paramètres
     */
    public static function getValue($key, $default = null)
    {
        $settings = self::getActive();
        return $settings ? $settings->$key : $default;
    }
}
