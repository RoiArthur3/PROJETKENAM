<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingLabel extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'receiving_address_id',
        'label_number',
        'sender_name',
        'sender_address',
        'sender_city',
        'sender_postal_code',
        'sender_country',
        'sender_phone',
        'sender_email',
        'parcel_contents',
        'declared_value',
        'weight',
        'tracking_number',
        'purchase_store',
        'purchase_date',
        'status',
        'pdf_path',
        'generated_at',
        'printed_at',
    ];

    protected $casts = [
        'declared_value' => 'decimal:2',
        'weight' => 'decimal:2',
        'purchase_date' => 'date',
        'generated_at' => 'datetime',
        'printed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function receivingAddress()
    {
        return $this->belongsTo(ReceivingAddress::class);
    }

    public function getFullSenderAddressAttribute()
    {
        return "{$this->sender_address}, {$this->sender_postal_code} {$this->sender_city}, {$this->sender_country}";
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeGenerated($query)
    {
        return $query->where('status', 'generated');
    }

    public function scopePrinted($query)
    {
        return $query->where('status', 'printed');
    }
}
