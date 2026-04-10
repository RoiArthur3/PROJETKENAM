<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParcelPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'parcel_id',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'order',
        'caption',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
            'order' => 'integer',
        ];
    }

    public function parcel()
    {
        return $this->belongsTo(Parcel::class);
    }
}
