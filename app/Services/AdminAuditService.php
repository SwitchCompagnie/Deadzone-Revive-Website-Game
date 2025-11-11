<?php

namespace App\Services;

use App\Models\AdminAuditLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AdminAuditService
{
    /**
     * Enregistrer une action d'audit
     */
    public static function log(
        string $action,
        ?Model $resource = null,
        ?string $resourceType = null,
        ?string $resourceName = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null,
        ?array $metadata = null
    ): AdminAuditLog {
        $user = Auth::user();

        // Si une ressource est fournie, extraire les informations
        if ($resource) {
            $resourceType = get_class($resource);
            $resourceId = $resource->getKey();
            $resourceTitle = self::getResourceTitle($resource);
        } else {
            $resourceId = null;
            $resourceTitle = null;
        }

        return AdminAuditLog::create([
            'user_id' => $user?->id,
            'user_name' => $user?->name ?? 'Système',
            'action' => $action,
            'resource_type' => $resourceType,
            'resource_name' => $resourceName,
            'resource_id' => $resourceId,
            'resource_title' => $resourceTitle,
            'description' => $description ?? self::generateDescription($action, $resourceName, $resourceTitle),
            'old_values' => $oldValues ? self::sanitizeValues($oldValues) : null,
            'new_values' => $newValues ? self::sanitizeValues($newValues) : null,
            'metadata' => $metadata,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'url' => Request::fullUrl(),
        ]);
    }

    /**
     * Enregistrer une consultation
     */
    public static function logView(?Model $resource = null, ?string $resourceName = null): AdminAuditLog
    {
        return self::log('view', $resource, null, $resourceName);
    }

    /**
     * Enregistrer une création
     */
    public static function logCreate(Model $resource, ?string $resourceName = null, ?array $values = null): AdminAuditLog
    {
        return self::log('create', $resource, null, $resourceName, null, $values ?? $resource->getAttributes());
    }

    /**
     * Enregistrer une modification
     */
    public static function logUpdate(Model $resource, ?string $resourceName = null, ?array $oldValues = null, ?array $newValues = null): AdminAuditLog
    {
        // Si les valeurs ne sont pas fournies, utiliser les changements du modèle
        if (!$oldValues && !$newValues && $resource->isDirty()) {
            $changes = $resource->getDirty();
            $oldValues = [];
            $newValues = [];

            foreach ($changes as $key => $newValue) {
                $oldValues[$key] = $resource->getOriginal($key);
                $newValues[$key] = $newValue;
            }
        }

        return self::log('update', $resource, null, $resourceName, $oldValues, $newValues);
    }

    /**
     * Enregistrer une suppression
     */
    public static function logDelete(Model $resource, ?string $resourceName = null): AdminAuditLog
    {
        return self::log('delete', $resource, null, $resourceName, $resource->getAttributes());
    }

    /**
     * Enregistrer une restauration
     */
    public static function logRestore(Model $resource, ?string $resourceName = null): AdminAuditLog
    {
        return self::log('restore', $resource, null, $resourceName);
    }

    /**
     * Obtenir un titre lisible pour une ressource
     */
    private static function getResourceTitle(Model $resource): ?string
    {
        // Essayer différents attributs communs pour le titre
        $titleAttributes = ['name', 'title', 'label', 'username', 'email', 'slug'];

        foreach ($titleAttributes as $attribute) {
            if (isset($resource->$attribute)) {
                return (string) $resource->$attribute;
            }
        }

        // Si aucun attribut trouvé, retourner l'ID
        return "#{$resource->getKey()}";
    }

    /**
     * Générer une description automatique
     */
    private static function generateDescription(string $action, ?string $resourceName, ?string $resourceTitle): string
    {
        $actionLabels = [
            'view' => 'a consulté',
            'create' => 'a créé',
            'update' => 'a modifié',
            'delete' => 'a supprimé',
            'restore' => 'a restauré',
        ];

        $actionLabel = $actionLabels[$action] ?? $action;
        $resource = $resourceName ?? 'ressource';
        $title = $resourceTitle ? " « {$resourceTitle} »" : '';

        return "{$actionLabel} {$resource}{$title}";
    }

    /**
     * Nettoyer les valeurs avant stockage (supprimer les champs sensibles)
     */
    private static function sanitizeValues(array $values): array
    {
        $sensitiveFields = ['password', 'password_confirmation', 'remember_token', 'token', 'api_token', 'secret'];

        foreach ($sensitiveFields as $field) {
            if (isset($values[$field])) {
                $values[$field] = '[MASQUÉ]';
            }
        }

        return $values;
    }

    /**
     * Comparer deux ensembles de valeurs et retourner uniquement les changements
     */
    public static function getChanges(array $old, array $new): array
    {
        $changes = [];

        foreach ($new as $key => $value) {
            if (!array_key_exists($key, $old) || $old[$key] !== $value) {
                $changes[$key] = [
                    'old' => $old[$key] ?? null,
                    'new' => $value,
                ];
            }
        }

        return $changes;
    }

    /**
     * Obtenir les statistiques d'audit pour un utilisateur
     */
    public static function getUserStats(?int $userId = null, ?string $period = 'week'): array
    {
        $query = AdminAuditLog::query();

        if ($userId) {
            $query->where('user_id', $userId);
        }

        // Filtrer par période
        if ($period) {
            $date = match ($period) {
                'day' => now()->subDay(),
                'week' => now()->subWeek(),
                'month' => now()->subMonth(),
                'year' => now()->subYear(),
                default => now()->subWeek(),
            };

            $query->where('created_at', '>=', $date);
        }

        return [
            'total' => $query->count(),
            'by_action' => $query->selectRaw('action, COUNT(*) as count')
                ->groupBy('action')
                ->pluck('count', 'action')
                ->toArray(),
            'by_resource' => $query->selectRaw('resource_name, COUNT(*) as count')
                ->whereNotNull('resource_name')
                ->groupBy('resource_name')
                ->pluck('count', 'resource_name')
                ->toArray(),
        ];
    }
}
