<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PieceJointe extends Model
{
    use HasFactory;

    protected $table = 'pieces_jointes';

    protected $fillable = [
        'requete_id',
        'nom_fichier',
        'chemin_fichier',
        'type_mime',
        'taille',
        'upload_par',
    ];

    public function requete(): BelongsTo
    {
        return $this->belongsTo(Requete::class, 'requete_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'upload_par');
    }
}
