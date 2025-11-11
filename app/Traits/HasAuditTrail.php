<?php

namespace App\Traits;

use App\Services\AdminAuditService;
use Illuminate\Database\Eloquent\Model;

trait HasAuditTrail
{
    protected static function afterCreate(Model $record): void
    {
        AdminAuditService::logCreate(
            $record,
            static::getAuditResourceName(),
            $record->getAttributes()
        );
    }

    protected static function afterUpdate(Model $record): void
    {
        $changes = $record->getChanges();

        if (empty($changes)) {
            return;
        }

        $oldValues = [];
        $newValues = [];

        foreach ($changes as $key => $newValue) {
            $oldValues[$key] = $record->getOriginal($key);
            $newValues[$key] = $newValue;
        }

        AdminAuditService::logUpdate(
            $record,
            static::getAuditResourceName(),
            $oldValues,
            $newValues
        );
    }

    protected static function afterDelete(Model $record): void
    {
        AdminAuditService::logDelete(
            $record,
            static::getAuditResourceName()
        );
    }

    protected static function afterRestore(Model $record): void
    {
        AdminAuditService::logRestore(
            $record,
            static::getAuditResourceName()
        );
    }

    protected static function getAuditResourceName(): string
    {
        if (method_exists(static::class, 'getCustomAuditName')) {
            return static::getCustomAuditName();
        }

        return static::getModelLabel();
    }
}
