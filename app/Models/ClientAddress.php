<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'address_type', // billing, shipping, pickup, return
        'address_line_1',
        'address_line_2',
        'city',
        'state_province',
        'country',
        'postal_code',
        'is_primary',
        'is_active',
        'contact_person',
        'phone',
        'email',
        'special_instructions',
        'coordinates_lat',
        'coordinates_lng',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_active' => 'boolean',
        'coordinates_lat' => 'decimal:8',
        'coordinates_lng' => 'decimal:8',
    ];

    /**
     * Relationship with Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get full address
     */
    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state_province,
            $this->country,
            $this->postal_code
        ]);

        return implode(', ', $parts);
    }

    /**
     * Set as primary address (unset others)
     */
    public function setAsPrimary(): void
    {
        static::where('client_id', $this->client_id)
            ->where('address_type', $this->address_type)
            ->update(['is_primary' => false]);
        
        $this->is_primary = true;
        $this->save();
    }
}
