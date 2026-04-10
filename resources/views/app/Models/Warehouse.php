<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'country',
        'phone',
        'manager',
        'surface_area',
        'capacity',
        'type',
        'is_active',
        'email',
        'opening_hours',
        'notes',
    ];

    protected $casts = [
        'surface_area' => 'float',
        'capacity' => 'float',
        'is_active' => 'boolean',
    ];

    // Compat: ancien champ manager_id remplacé par manager (string). On garde une
    // relation éventuelle vers User si un manager_id existe encore.
    public function managerUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    // Polymorphic relations to stock balances and movements via location
    public function balances()
    {
        return $this->morphMany(StockBalance::class, 'location');
    }

    public function movements()
    {
        return $this->morphMany(StockMovement::class, 'from_location');
    }

    // Compat: ancien schéma utilisait nom/localisation/capacite/actif
    public function getNomAttribute()
    {
        return $this->name;
    }

    public function setNomAttribute($value): void
    {
        $this->attributes['name'] = $value;
    }

    public function getLocalisationAttribute()
    {
        return $this->address;
    }

    public function setLocalisationAttribute($value): void
    {
        $this->attributes['address'] = $value;
    }

    public function getCapaciteAttribute()
    {
        return $this->capacity;
    }

    public function setCapaciteAttribute($value): void
    {
        $this->attributes['capacity'] = $value;
    }

    public function getActifAttribute()
    {
        return $this->is_active;
    }

    public function setActifAttribute($value): void
    {
        $this->attributes['is_active'] = (bool) $value;
    }

    /**
     * Scope pour les entrepôts actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
