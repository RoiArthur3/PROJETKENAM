<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use App\Models\User;

class HeureSup extends Model
{
    use HasFactory;

    protected $table = 'heures_sup';

    protected $fillable = [
        'user_id',
        'date',
        'heure_debut',
        'heure_fin',
        'duree_minutes',
        'type',
        'service',
        'majoration',
        'montant',
        'statut',
        'commentaire',
        'validated_by',
        'validated_at',
        'paid_at',
    ];

    protected $casts = [
        'date' => 'date',
        'heure_debut' => 'datetime:H:i',
        'heure_fin' => 'datetime:H:i',
        'duree_minutes' => 'integer',
        'majoration' => 'integer',
        'montant' => 'integer',
        'validated_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function validator()
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function getDureeAttribute(): ?string
    {
        if (!$this->duree_minutes) {
            return null;
        }

        $heures = intdiv($this->duree_minutes, 60);
        $minutes = $this->duree_minutes % 60;

        return sprintf('%dh%02d', $heures, $minutes);
    }

    public function getStatutLabelAttribute(): string
    {
        $statut = $this->statut ?? 'en_attente';

        return ucfirst(str_replace('_', ' ', $statut));
    }

    public function scopeForMonth($query, Carbon|string $month)
    {
        if (is_string($month)) {
            // Utilise parse pour accepter "Y-m" ou "Y-m-d" sans erreur "Trailing data"
            $month = Carbon::parse($month)->startOfMonth();
        }

        return $query
            ->whereYear('date', $month->year)
            ->whereMonth('date', $month->month);
    }

    public function scopeValidated($query)
    {
        return $query->where('statut', 'valide');
    }

    public function scopePaid($query)
    {
        return $query->where('statut', 'paye');
    }
}
