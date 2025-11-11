<?php

namespace App\Traits;

use App\Services\AdminAuditService;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

/**
 * Trait à ajouter aux ressources Filament pour activer l'audit automatique
 */
trait HasAuditTrail
{
    /**
     * Hook appelé après la création d'un enregistrement
     */
    protected static function afterCreate(Model $record): void
    {
        AdminAuditService::logCreate(
            $record,
            static::getAuditResourceName(),
            $record->getAttributes()
        );
    }

    /**
     * Hook appelé après la modification d'un enregistrement
     */
    protected static function afterUpdate(Model $record): void
    {
        // Récupérer les changements depuis la dernière sauvegarde
        $changes = $record->getChanges();

        if (empty($changes)) {
            return; // Pas de changements, pas de log
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

    /**
     * Hook appelé après la suppression d'un enregistrement
     */
    protected static function afterDelete(Model $record): void
    {
        AdminAuditService::logDelete(
            $record,
            static::getAuditResourceName()
        );
    }

    /**
     * Hook appelé après la restauration d'un enregistrement (soft delete)
     */
    protected static function afterRestore(Model $record): void
    {
        AdminAuditService::logRestore(
            $record,
            static::getAuditResourceName()
        );
    }

    /**
     * Obtenir le nom lisible de la ressource pour l'audit
     */
    protected static function getAuditResourceName(): string
    {
        // Si une méthode personnalisée existe, l'utiliser
        if (method_exists(static::class, 'getCustomAuditName')) {
            return static::getCustomAuditName();
        }

        // Sinon, utiliser le label du modèle de Filament
        return static::getModelLabel();
    }
}
