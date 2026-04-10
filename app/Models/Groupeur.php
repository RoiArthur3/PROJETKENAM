<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Groupeur extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'business_name',
        'contact_person',
        'phone',
        'email',
        'business_card_path',
        'business_card_uploaded_at',
        'shipping_address',
        'shipping_city',
        'shipping_country',
        'shipping_postal_code',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'business_card_uploaded_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBusinessCardUrlAttribute()
    {
        if (!$this->business_card_path) {
            return null;
        }

        return asset('storage/' . $this->business_card_path);
    }

    public function getFullShippingAddressAttribute()
    {
        $parts = array_filter([
            $this->shipping_address,
            $this->shipping_city,
            $this->shipping_postal_code,
            $this->shipping_country,
        ]);

        return implode(', ', $parts);
    }
}
