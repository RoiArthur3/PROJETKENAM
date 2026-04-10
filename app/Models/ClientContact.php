<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'contact_type', // primary, billing, technical, logistics
        'first_name',
        'last_name',
        'position',
        'phone',
        'mobile',
        'email',
        'is_primary',
        'is_active',
        'preferred_contact_method', // email, phone, sms
        'language_preference',
        'timezone',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Relationship with Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . $this->last_name);
    }

    /**
     * Set as primary contact (unset others)
     */
    public function setAsPrimary(): void
    {
        static::where('client_id', $this->client_id)
            ->update(['is_primary' => false]);
        
        $this->is_primary = true;
        $this->save();
    }
}
