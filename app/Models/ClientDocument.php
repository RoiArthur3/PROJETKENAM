<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'document_type', // id_card, passport, commercial_register, tax_certificate, contract, other
        'document_name',
        'file_path',
        'file_size',
        'mime_type',
        'document_number',
        'issue_date',
        'expiry_date',
        'issuing_authority',
        'status', // pending, verified, rejected, expired
        'verification_notes',
        'uploaded_by',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'verified_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * Relationship with Client
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relationship with uploader
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Relationship with verifier
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    /**
     * Check if document is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    /**
     * Check if document is verified
     */
    public function isVerified(): bool
    {
        return $this->status === 'verified';
    }

    /**
     * Mark document as verified
     */
    public function markAsVerified(int $verifiedBy, string $notes = null): void
    {
        $this->status = 'verified';
        $this->verified_by = $verifiedBy;
        $this->verified_at = now();
        $this->verification_notes = $notes;
        $this->save();
    }

    /**
     * Mark document as rejected
     */
    public function markAsRejected(string $notes): void
    {
        $this->status = 'rejected';
        $this->verification_notes = $notes;
        $this->save();
    }
}
