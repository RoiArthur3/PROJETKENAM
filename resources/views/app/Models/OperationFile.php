<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OperationFile extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'operation_files';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'operation_id',
        'nom_original',
        'chemin',
        'type_fichier',
        'taille',
        'uploaded_by',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'taille' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'opération
     */
    public function operation(): BelongsTo
    {
        return $this->belongsTo(Operation::class);
    }

    /**
     * Obtenir la taille formatée
     */
    public function getTailleFormateeAttribute(): string
    {
        $bytes = $this->taille;
        if ($bytes === 0) return '0 Bytes';

        $units = ['Bytes', 'KB', 'MB', 'GB'];
        $factor = floor((strlen($bytes) - 1) / 3);

        return sprintf("%.2f %s", $bytes / pow(1024, $factor), $units[$factor]);
    }

    /**
     * Obtenir l'icône selon le type de fichier
     */
    public function getIconeAttribute(): string
    {
        $extension = strtolower(pathinfo($this->nom_original, PATHINFO_EXTENSION));

        $icones = [
            'pdf' => 'fas fa-file-pdf',
            'doc' => 'fas fa-file-word',
            'docx' => 'fas fa-file-word',
            'xls' => 'fas fa-file-excel',
            'xlsx' => 'fas fa-file-excel',
            'csv' => 'fas fa-file-csv',
            'jpg' => 'fas fa-file-image',
            'jpeg' => 'fas fa-file-image',
            'png' => 'fas fa-file-image',
            'gif' => 'fas fa-file-image',
            'txt' => 'fas fa-file-alt',
            'zip' => 'fas fa-file-archive',
            'rar' => 'fas fa-file-archive',
        ];

        return $icones[$extension] ?? 'fas fa-file';
    }

    /**
     * Obtenir l'URL du fichier
     */
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->chemin);
    }

    /**
     * Vérifier si le fichier est une image
     */
    public function estImage(): bool
    {
        $extension = strtolower(pathinfo($this->nom_original, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /**
     * Vérifier si le fichier est un PDF
     */
    public function estPdf(): bool
    {
        $extension = strtolower(pathinfo($this->nom_original, PATHINFO_EXTENSION));
        return $extension === 'pdf';
    }

    /**
     * Vérifier si le fichier est un document Excel
     */
    public function estExcel(): bool
    {
        $extension = strtolower(pathinfo($this->nom_original, PATHINFO_EXTENSION));
        return in_array($extension, ['xls', 'xlsx', 'csv']);
    }
}
