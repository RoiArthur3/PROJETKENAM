<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VehicleIncident extends Model
{
    protected $fillable = [
        'vehicle_id','reported_by','type','date_incident','severity','description','status','attachment','estimated_cost','final_cost'
    ];

    protected $casts = [
        'date_incident' => 'date',
        'estimated_cost' => 'decimal:2',
        'final_cost' => 'decimal:2',
    ];

    public function vehicle(): BelongsTo { return $this->belongsTo(Vehicle::class); }
    public function reporter(): BelongsTo { return $this->belongsTo(User::class, 'reported_by'); }
}
