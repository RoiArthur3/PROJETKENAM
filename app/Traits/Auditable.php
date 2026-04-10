<?php

namespace App\Traits;

use App\Models\AuditLog;
use Illuminate\Database\Eloquent\Model;

trait Auditable
{
    /**
     * Boot the auditable trait for a model.
     */
    protected static function bootAuditable()
    {
        static::created(function (Model $model) {
            if (self::shouldAudit('created')) {
                AuditLog::log('created', $model, null, $model->getAttributes());
            }
        });

        static::updated(function (Model $model) {
            if (self::shouldAudit('updated')) {
                $changes = $model->getDirty();
                $original = $model->getOriginal();
                
                // Filter out sensitive fields
                $filteredChanges = collect($changes)->except(['password', 'remember_token'])->toArray();
                $filteredOriginal = collect($original)->only(array_keys($filteredChanges))->toArray();
                
                if (!empty($filteredChanges)) {
                    AuditLog::log('updated', $model, $filteredOriginal, $filteredChanges);
                }
            }
        });

        static::deleted(function (Model $model) {
            if (self::shouldAudit('deleted')) {
                AuditLog::log('deleted', $model, $model->getAttributes(), null);
            }
        });
    }

    /**
     * Get the audit logs for this model.
     */
    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }

    /**
     * Get the latest audit log for this model.
     */
    public function latestAuditLog()
    {
        return $this->auditLogs()->latest()->first();
    }

    /**
     * Log a custom action for this model.
     */
    public function logAudit($action, $oldValues = null, $newValues = null)
    {
        return AuditLog::log($action, $this, $oldValues, $newValues);
    }

    /**
     * Determine if the model should audit the given action.
     */
    protected static function shouldAudit($action)
    {
        // Check if auditing is enabled globally
        if (!config('audit.enabled', true)) {
            return false;
        }

        // Check if the action is in the excluded actions
        $excludedActions = config('audit.excluded_actions', []);
        if (in_array($action, $excludedActions)) {
            return false;
        }

        // Check if the model is in the excluded models
        $excludedModels = config('audit.excluded_models', []);
        $modelClass = static::class;
        if (in_array($modelClass, $excludedModels)) {
            return false;
        }

        return true;
    }

    /**
     * Get the audit display name for this model.
     */
    public function getAuditDisplayName()
    {
        if (method_exists($this, 'getName')) {
            return $this->getName();
        }
        
        if (isset($this->name)) {
            return $this->name;
        }
        
        if (isset($this->reference)) {
            return $this->reference;
        }
        
        return class_basename($this) . ' #' . $this->id;
    }
}
