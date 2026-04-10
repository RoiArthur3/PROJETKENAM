<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'code3',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function productKeywords()
    {
        return $this->belongsToMany(ProductKeyword::class, 'country_product_keyword')
            ->withPivot(['restriction_type', 'notes'])
            ->withTimestamps();
    }

    public function allowedProductKeywords()
    {
        return $this->belongsToMany(ProductKeyword::class, 'country_product_keyword')
            ->wherePivot('restriction_type', 'allowed')
            ->withPivot(['notes'])
            ->withTimestamps();
    }

    public function forbiddenProductKeywords()
    {
        return $this->belongsToMany(ProductKeyword::class, 'country_product_keyword')
            ->wherePivot('restriction_type', 'forbidden')
            ->withPivot(['notes'])
            ->withTimestamps();
    }

    public function forbiddenInCountries()
    {
        return $this->belongsToMany(Country::class, 'country_product_keyword')
            ->wherePivot('restriction_type', 'forbidden')
            ->withPivot(['notes'])
            ->withTimestamps();
    }

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function activeCities()
    {
        return $this->hasMany(City::class)->where('is_active', true);
    }
}
