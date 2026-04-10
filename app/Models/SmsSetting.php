<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SmsSetting extends Model
{
    use HasFactory;

    protected $table = 'sms_settings';

    protected $fillable = [
        'provider',
        'sender_id',
        'api_url',
        'api_username',
        'api_password',
        'sms_enabled',
        'sandbox_mode',
        'default_template',
        'fallback_number',
    ];

    protected $casts = [
        'sms_enabled' => 'boolean',
        'sandbox_mode' => 'boolean',
    ];

    /**
     * Récupérer la configuration active (unique ligne attendue).
     */
    public static function current(): ?self
    {
        return static::query()->latest('id')->first();
    }
}
