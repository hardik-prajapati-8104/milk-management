<?php

namespace App\Traits;

use App\Models\ActivityLog;

/**
 * Add `use Auditable;` to any Eloquent model to get automatic, field-level
 * Audit Trail entries (log_type = 'audit') on create/update/delete — no
 * per-controller wiring required, so no module can be accidentally skipped.
 *
 * This is separate from the human-readable Activity Log entries some
 * controllers/services already write manually (log_type = 'activity');
 * both are kept because they serve different purposes: Activity Log reads
 * like "Customer C-104 created by Priya", Audit Trail shows the exact
 * field-by-field before/after values for compliance/security review.
 *
 * Models can override `$auditExclude` to keep noisy or sensitive columns
 * (secrets, hashes, timestamps) out of the recorded diff.
 */
trait Auditable
{
    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->writeAuditLog('created', [], $model->auditableAttributes());
        });

        static::updated(function ($model) {
            $changes = $model->auditableChanges();

            if (empty($changes['old']) && empty($changes['new'])) {
                return;
            }

            $model->writeAuditLog('updated', $changes['old'], $changes['new']);
        });

        static::deleted(function ($model) {
            $model->writeAuditLog('deleted', $model->auditableAttributes(), []);
        });
    }

    protected function writeAuditLog(string $action, array $old, array $new): void
    {
        ActivityLog::record(
            action: $action,
            subject: $this,
            old: $old,
            new: $new,
            description: class_basename($this) . " {$action}",
            logType: 'audit',
        );
    }

    /**
     * Attributes for this model with excluded fields stripped out.
     */
    protected function auditableAttributes(): array
    {
        $exclude = $this->auditExcludedFields();

        return collect($this->attributesToArray())
            ->except($exclude)
            ->toArray();
    }

    /**
     * Only the fields that actually changed on this update, with sensitive/
     * excluded fields stripped from both the old and new side.
     */
    protected function auditableChanges(): array
    {
        $exclude = $this->auditExcludedFields();
        $dirty = collect($this->getChanges())->except($exclude);

        $old = [];
        $new = [];

        foreach ($dirty->keys() as $key) {
            $old[$key] = $this->getOriginal($key);
            $new[$key] = $this->getAttribute($key);
        }

        return ['old' => $old, 'new' => $new];
    }

    protected function auditExcludedFields(): array
    {
        $defaults = ['updated_at', 'created_at', 'password', 'remember_token', 'two_factor_secret'];

        return array_unique(array_merge($defaults, $this->auditExclude ?? []));
    }
}
