<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceivingAddress extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'city',
        'postal_code',
        'country',
        'phone',
        'email',
        'contact_person',
        'instructions',
        'warehouse_code',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function shippingLabels()
    {
        return $this->hasMany(ShippingLabel::class);
    }

    public function getFullAddressAttribute()
    {
        return "{$this->address}, {$this->postal_code} {$this->city}, {$this->country}";
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }
}
