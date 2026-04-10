<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParcelStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'parcel_status_history';

    protected $fillable = [
        'parcel_id',
        'old_status',
        'new_status',
        'changed_by',
        'comment',
        'ip_address',
    ];

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
