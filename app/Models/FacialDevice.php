<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FacialDevice extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'serial_number',
        'ip_address',
        'port',
        'protocol',
        'username',
        'password',
        'api_token',
        'is_active',
        'last_seen_at',
        'last_status',
        'last_error',
        'settings',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_seen_at' => 'datetime',
        'settings' => 'array',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(FacialEvent::class);
    }
}
