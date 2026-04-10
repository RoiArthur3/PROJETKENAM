<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleMission extends Model
{
    protected $fillable = [
        'vehicle_id','user_id','reference','destination','start_at','end_at','objective','status','start_km','end_km','notes'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'start_km' => 'integer',
        'end_km' => 'integer',
    ];

    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
