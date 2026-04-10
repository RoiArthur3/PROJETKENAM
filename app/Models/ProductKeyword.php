<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'keyword',
        'status',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function countries()
    {
        return $this->belongsToMany(Country::class, 'country_product_keyword')
            ->withPivot(['restriction_type', 'notes'])
            ->withTimestamps();
    }

    public function allowedInCountries()
    {
        return $this->belongsToMany(Country::class, 'country_product_keyword')
            ->wherePivot('restriction_type', 'allowed')
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

    public function isAllowedInDestinationCountry($countryCode)
    {
        $country = Country::where('code', $countryCode)->first();
        if (!$country) {
            return true; // Si le pays n'est pas dans la base, on autorise par défaut
        }

        $restriction = $this->countries()
            ->where('countries.id', $country->id)
            ->first();

        if (!$restriction) {
            return $this->status === 'allowed'; // Statut par défaut si pas de restriction spécifique
        }

        return $restriction->pivot->restriction_type === 'allowed';
    }

    public function isForbiddenInDestinationCountry($countryCode)
    {
        return !$this->isAllowedInDestinationCountry($countryCode);
    }
}
