<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'action',
        'resource_type',
        'resource_name',
        'resource_id',
        'resource_title',
        'description',
        'old_values',
        'new_values',
        'metadata',
        'ip_address',
        'user_agent',
        'url',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation avec l'utilisateur qui a effectué l'action
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Obtenir le nom d'affichage de l'action
     */
    public function getActionLabelAttribute(): string
    {
        return match ($this->action) {
            'view' => 'Consultation',
            'create' => 'Création',
            'update' => 'Modification',
            'delete' => 'Suppression',
            'restore' => 'Restauration',
            'replicate' => 'Duplication',
            'force_delete' => 'Suppression définitive',
            'attach' => 'Attachement',
            'detach' => 'Détachement',
            default => ucfirst($this->action),
        };
    }

    /**
     * Obtenir le badge de couleur pour l'action
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'view' => 'info',
            'create' => 'success',
            'update' => 'warning',
            'delete', 'force_delete' => 'danger',
            'restore' => 'success',
            default => 'gray',
        };
    }

    /**
     * Scope pour filtrer par utilisateur
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope pour filtrer par type de ressource
     */
    public function scopeByResourceType($query, $resourceType)
    {
        return $query->where('resource_type', $resourceType);
    }

    /**
     * Scope pour filtrer par action
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }
}
