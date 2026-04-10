<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'path',
        'size',
        'mime_type',
        'user_id',
    ];

    protected $keyType = 'string';
    public $incrementing = false;

    protected $casts = [
        'size' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSizeInKB()
    {
        return round($this->size / 1024, 2) . ' KB';
    }

    public function getSizeInMB()
    {
        return round($this->size / 1024 / 1024, 2) . ' MB';
    }

    public function getFormattedSize()
    {
        if ($this->size < 1024 * 1024) {
            return $this->getSizeInKB();
        } else {
            return $this->getSizeInMB();
        }
    }

    public function getFileIcon()
    {
        if (str_starts_with($this->mime_type, 'image/')) {
            return 'fas fa-image text-success';
        } elseif (str_starts_with($this->mime_type, 'video/')) {
            return 'fas fa-video text-danger';
        } elseif (str_contains($this->mime_type, 'pdf')) {
            return 'fas fa-file-pdf text-danger';
        } elseif (str_contains($this->mime_type, 'word') || str_contains($this->mime_type, 'document')) {
            return 'fas fa-file-word text-primary';
        } elseif (str_contains($this->mime_type, 'excel') || str_contains($this->mime_type, 'spreadsheet')) {
            return 'fas fa-file-excel text-success';
        } elseif (str_contains($this->mime_type, 'powerpoint') || str_contains($this->mime_type, 'presentation')) {
            return 'fas fa-file-powerpoint text-warning';
        } elseif (str_contains($this->mime_type, 'zip') || str_contains($this->mime_type, 'rar') || str_contains($this->mime_type, 'tar')) {
            return 'fas fa-file-archive text-info';
        } else {
            return 'fas fa-file text-secondary';
        }
    }
}
