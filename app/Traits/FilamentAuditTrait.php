<?php

namespace App\Traits;

use App\Services\AdminAuditService;
use Illuminate\Database\Eloquent\Model;

trait FilamentAuditTrait
{
    protected function afterCreate(): void
    {
        if (method_exists(parent::class, 'afterCreate')) {
            parent::afterCreate();
        }

        AdminAuditService::logCreate(
            $this->record,
            $this->getAuditResourceName(),
            $this->record->getAttributes()
        );
    }

    protected function afterSave(): void
    {
        if (method_exists(parent::class, 'afterSave')) {
            parent::afterSave();
        }

        if (!$this->record->wasRecentlyCreated) {
            $changes = $this->record->getChanges();

            if (!empty($changes)) {
                $oldValues = [];
                $newValues = [];

                foreach ($changes as $key => $newValue) {
                    $oldValues[$key] = $this->record->getOriginal($key);
                    $newValues[$key] = $newValue;
                }

                AdminAuditService::logUpdate(
                    $this->record,
                    $this->getAuditResourceName(),
                    $oldValues,
                    $newValues
                );
            }
        }
    }

    protected function afterDelete(): void
    {
        if (method_exists(parent::class, 'afterDelete')) {
            parent::afterDelete();
        }

        AdminAuditService::logDelete(
            $this->record,
            $this->getAuditResourceName()
        );
    }

    protected function getAuditResourceName(): string
    {
        $resource = static::getResource();

        if (method_exists($resource, 'getModelLabel')) {
            return $resource::getModelLabel();
        }

        return class_basename($this->record);
    }
}
