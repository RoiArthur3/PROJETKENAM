<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLock extends Model
{
    use HasFactory;

    protected $fillable = [
        'lock_type',
        'target',
        'is_locked',
        'reason',
        'lock_code',
        'code_expires_at',
        'locked_by',
        'locked_at',
        'unlocked_at',
        'unlocked_by',
    ];

    protected $casts = [
        'is_locked' => 'boolean',
        'locked_at' => 'datetime',
        'unlocked_at' => 'datetime',
        'code_expires_at' => 'datetime',
    ];

    // Types de blocage
    const TYPE_FULL_ACCESS = 'full_access';
    const TYPE_MODULE_ACCESS = 'module_access';
    const TYPE_USER_ACCESS = 'user_access';

    /**
     * Relation avec l'utilisateur qui a bloqué
     */
    public function locker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Relation avec l'utilisateur qui a débloqué
     */
    public function unlocker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'unlocked_by');
    }

    /**
     * Vérifier si un accès est bloqué
     */
    public static function isLocked(string $lockType, ?string $target = null): bool
    {
        return self::where('lock_type', $lockType)
            ->where('target', $target)
            ->where('is_locked', true)
            ->exists();
    }

    /**
     * Bloquer un accès
     */
    public static function lock(string $lockType, ?string $target, string $reason, int $userId): self
    {
        // Débloquer d'abord si déjà bloqué
        self::where('lock_type', $lockType)
            ->where('target', $target)
            ->where('is_locked', true)
            ->update(['is_locked' => false, 'unlocked_at' => now()]);

        return self::create([
            'lock_type' => $lockType,
            'target' => $target,
            'is_locked' => true,
            'reason' => $reason,
            'locked_by' => $userId,
            'locked_at' => now(),
        ]);
    }

    /**
     * Débloquer un accès
     */
    public static function unlock(string $lockType, ?string $target, int $userId): bool
    {
        $lock = self::where('lock_type', $lockType)
            ->where('target', $target)
            ->where('is_locked', true)
            ->first();

        if ($lock) {
            $lock->update([
                'is_locked' => false,
                'unlocked_at' => now(),
                'unlocked_by' => $userId,
            ]);
            return true;
        }

        return false;
    }

    /**
     * Obtenir tous les blocages actifs
     */
    public static function getActiveLocks()
    {
        return self::where('is_locked', true)
            ->with(['locker', 'unlocker'])
            ->orderBy('locked_at', 'desc')
            ->get();
    }

    /**
     * Obtenir le libellé du type de blocage
     */
    public function getLockTypeLabelAttribute(): string
    {
        return match($this->lock_type) {
            self::TYPE_FULL_ACCESS => 'Accès complet',
            self::TYPE_MODULE_ACCESS => 'Accès module',
            self::TYPE_USER_ACCESS => 'Accès utilisateur',
            default => 'Inconnu',
        };
    }

    /**
     * Obtenir la description de la cible
     */
    public function getTargetDescriptionAttribute(): string
    {
        return match($this->lock_type) {
            self::TYPE_FULL_ACCESS => 'Système entier',
            self::TYPE_MODULE_ACCESS => $this->getModuleLabel(),
            self::TYPE_USER_ACCESS => $this->getUserLabel(),
            default => 'Inconnu',
        };
    }

    /**
     * Obtenir le libellé du module
     */
    private function getModuleLabel(): string
    {
        $modules = \App\Services\UserPermissionService::getAllModules();
        return $modules[$this->target]['label'] ?? $this->target;
    }

    /**
     * Obtenir le libellé de l'utilisateur
     */
    private function getUserLabel(): string
    {
        $user = \App\Models\User::find($this->target);
        return $user ? $user->name : $this->target;
    }

    /**
     * Générer un code de blocage sécurisé
     */
    public static function generateLockCode(): string
    {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
    }

    /**
     * Vérifier si un code de blocage est valide
     */
    public static function validateLockCode(string $code): bool
    {
        $lock = self::where('lock_code', $code)
            ->where('is_locked', true)
            ->where('code_expires_at', '>', now())
            ->first();

        return $lock !== null;
    }

    /**
     * Obtenir le blocage par code
     */
    public static function findByCode(string $code): ?self
    {
        return self::where('lock_code', $code)
            ->where('is_locked', true)
            ->where('code_expires_at', '>', now())
            ->with(['locker'])
            ->first();
    }

    /**
     * Bloquer un accès avec code
     */
    public static function lockWithCode(string $lockType, ?string $target, string $reason, int $userId, int $minutes = 60): self
    {
        // Débloquer d'abord si déjà bloqué
        self::where('lock_type', $lockType)
            ->where('target', $target)
            ->where('is_locked', true)
            ->update(['is_locked' => false, 'unlocked_at' => now()]);

        $lockCode = self::generateLockCode();
        $expiresAt = now()->addMinutes($minutes);

        return self::create([
            'lock_type' => $lockType,
            'target' => $target,
            'is_locked' => true,
            'reason' => $reason,
            'lock_code' => $lockCode,
            'code_expires_at' => $expiresAt,
            'locked_by' => $userId,
            'locked_at' => now(),
        ]);
    }

    /**
     * Débloquer avec code
     */
    public static function unlockWithCode(string $code, int $userId): bool
    {
        $lock = self::findByCode($code);

        if ($lock) {
            $lock->update([
                'is_locked' => false,
                'unlocked_at' => now(),
                'unlocked_by' => $userId,
                'lock_code' => null, // Effacer le code après utilisation
                'code_expires_at' => null,
            ]);
            return true;
        }

        return false;
    }

    /**
     * Nettoyer les codes expirés
     */
    public static function cleanupExpiredCodes(): int
    {
        return self::where('code_expires_at', '<', now())
            ->whereNotNull('lock_code')
            ->update([
                'lock_code' => null,
                'code_expires_at' => null,
            ]);
    }

    /**
     * Vérifier si le code est expiré
     */
    public function isCodeExpired(): bool
    {
        return $this->code_expires_at && $this->code_expires_at->isPast();
    }

    /**
     * Obtenir le temps restant pour le code
     */
    public function getCodeTimeRemaining(): string
    {
        if (!$this->code_expires_at) {
            return 'N/A';
        }

        if ($this->isCodeExpired()) {
            return 'Expiré';
        }

        $remaining = $this->code_expires_at->diffForHumans(now(), true);
        return $remaining;
    }
}
