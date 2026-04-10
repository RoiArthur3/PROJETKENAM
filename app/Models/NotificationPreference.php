<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'sms_enabled',
        'phone_number',
        'sms_events',
        'whatsapp_enabled',
        'whatsapp_number',
        'whatsapp_events',
        'email_enabled',
        'email_events',
    ];

    protected $casts = [
        'sms_enabled' => 'boolean',
        'whatsapp_enabled' => 'boolean',
        'email_enabled' => 'boolean',
        'sms_events' => 'array',
        'whatsapp_events' => 'array',
        'email_events' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getDefaultEvents()
    {
        return [
            'parcel_declared' => 'Colis déclaré',
            'parcel_received' => 'Colis reçu à l\'entrepôt',
            'parcel_inspected' => 'Colis inspecté',
            'parcel_grouped' => 'Colis groupé pour expédition',
            'parcel_shipped' => 'Colis expédié',
            'parcel_in_transit' => 'Colis en transit',
            'parcel_arrived' => 'Colis arrivé au pays',
            'parcel_customs' => 'Colis en douane',
            'parcel_delivered' => 'Colis livré',
            'payment_required' => 'Paiement requis',
            'payment_confirmed' => 'Paiement confirmé',
        ];
    }

    public function isSmsEventEnabled($event)
    {
        if (!$this->sms_enabled || !$this->phone_number) {
            return false;
        }

        return in_array($event, $this->sms_events ?? []);
    }

    public function isWhatsAppEventEnabled($event)
    {
        if (!$this->whatsapp_enabled || !$this->whatsapp_number) {
            return false;
        }

        return in_array($event, $this->whatsapp_events ?? []);
    }

    public function isEmailEventEnabled($event)
    {
        if (!$this->email_enabled) {
            return false;
        }

        return in_array($event, $this->email_events ?? []);
    }
}
