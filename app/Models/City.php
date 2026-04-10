<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'country_id',
        'is_active',
        'latitude',
        'longitude',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function originParcels()
    {
        return $this->hasMany(Parcel::class, 'origin_city_id');
    }

    public function destinationParcels()
    {
        return $this->hasMany(Parcel::class, 'destination_city_id');
    }

    // Scope pour les villes actives
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope pour les villes d'un pays
    public function scopeInCountry($query, $countryId)
    {
        return $query->where('country_id', $countryId);
    }
}
