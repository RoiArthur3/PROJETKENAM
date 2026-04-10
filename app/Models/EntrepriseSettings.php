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
        'tva_rate',
        'email_tresorerie',
        'email_caisse_1',
        'email_caisse_2',
        'email_destinataire_principal',
        'email_dg',
        'seuil_validation_dg',
        'seuil_validation_principal',
        'seuil_validation_dg_force',
        'email_noreply',
        'email_support',
        'telephone_support',
        // Configuration SMS NetSMSPro
        'sms_provider',
        'sms_api_key',
        'sms_api_secret',
        'sms_username',
        'sms_password',
        'sms_sender_id',
        'sms_reseller_code',
        'sms_api_url',
        'sms_is_active',
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
