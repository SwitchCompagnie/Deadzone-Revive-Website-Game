<?php

namespace App\Traits;

use App\Services\AdminAuditService;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait à ajouter aux pages Filament (CreateRecord, EditRecord, etc.)
 * pour activer l'audit automatique des opérations CRUD
 */
trait FilamentAuditTrait
{
    /**
     * Hook appelé après la création d'un enregistrement
     */
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

    /**
     * Hook appelé après la modification d'un enregistrement
     */
    protected function afterSave(): void
    {
        if (method_exists(parent::class, 'afterSave')) {
            parent::afterSave();
        }

        // Uniquement pour les mises à jour (pas les créations)
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

    /**
     * Hook appelé après la suppression d'un enregistrement
     */
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

    /**
     * Obtenir le nom lisible de la ressource pour l'audit
     */
    protected function getAuditResourceName(): string
    {
        $resource = static::getResource();

        if (method_exists($resource, 'getModelLabel')) {
            return $resource::getModelLabel();
        }

        return class_basename($this->record);
    }
}
