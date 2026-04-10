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
        'nom',
        'chemin',
        'type_mime',
        'taille',
        'extension',
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
        $extension = strtolower($this->extension);

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
     * Utilise la route /files/{path} pour contourner le problème de junction Windows avec Apache
     */
    public function getUrlAttribute(): string
    {
        try {
            // Utiliser la route dédiée qui lit directement depuis le disque public
            return route('storage.file', ['path' => $this->chemin]);
        } catch (\Exception $e) {
            // Fallback vers Storage::url si la route n'existe pas
            return \Illuminate\Support\Facades\Storage::disk('public')->url($this->chemin);
        }
    }

    /**
     * Vérifier si le fichier est une image
     */
    public function estImage(): bool
    {
        $extension = strtolower($this->extension);
        $mime = strtolower($this->type_mime);
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) || str_starts_with($mime, 'image/');
    }

    /**
     * Vérifier si le fichier est un PDF
     */
    public function estPdf(): bool
    {
        $extension = strtolower($this->extension);
        $mime = strtolower($this->type_mime);
        return $extension === 'pdf' || $mime === 'application/pdf';
    }

    /**
     * Vérifier si le fichier est un document Excel
     */
    public function estExcel(): bool
    {
        $extension = strtolower($this->extension);
        $mime = strtolower($this->type_mime);
        
        $excelExtensions = ['xls', 'xlsx', 'csv'];
        $excelMimes = [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/csv'
        ];
        
        return in_array($extension, $excelExtensions) || in_array($mime, $excelMimes);
    }
}
