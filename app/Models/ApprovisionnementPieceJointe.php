<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovisionnementPieceJointe extends Model
{
    protected $table = 'approvisionnement_pieces_jointes';

    protected $fillable = [
        'approvisionnement_id',
        'nom_original',
        'chemin',
        'mime_type',
        'taille',
        'upload_par',
    ];

    protected $casts = [
        'taille' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function approvisionnement(): BelongsTo
    {
        return $this->belongsTo(ApprovisionnementCaisse::class, 'approvisionnement_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'upload_par');
    }

    public function getTailleFormateeAttribute(): string
    {
        $bytes = $this->taille;
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->chemin);
    }
}
