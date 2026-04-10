<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacialEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'facial_device_id',
        'serial_number',
        'employee_code',
        'employee_name',
        'event_time',
        'event_type',
        'direction',
        'status',
        'confidence',
        'raw_payload',
        'processed_at',
        'processing_status',
        'processing_message',
        'pointage_id',
    ];

    protected $casts = [
        'event_time' => 'datetime',
        'processed_at' => 'datetime',
        'confidence' => 'decimal:2',
        'raw_payload' => 'array',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(FacialDevice::class, 'facial_device_id');
    }

    public function pointage(): BelongsTo
    {
        return $this->belongsTo(Pointage::class);
    }
}
