# Guide du système de traçabilité (Audit Trail)

## Vue d'ensemble

Ce système permet de tracer toutes les actions effectuées par les administrateurs sur le panel admin Filament. Il enregistre automatiquement :

- **Les consultations** : Quelles pages ont été visitées
- **Les créations** : Nouveaux enregistrements créés
- **Les modifications** : Changements apportés aux enregistrements existants
- **Les suppressions** : Enregistrements supprimés

## Accès aux traces d'audit

Les traces d'audit sont accessibles depuis le menu "Administration" > "Traces d'audit" dans le panel admin Filament.

### Fonctionnalités disponibles

- **Filtrage par action** : Consultation, Création, Modification, Suppression
- **Filtrage par utilisateur** : Voir les actions d'un admin spécifique
- **Filtrage par ressource** : Voir les actions sur un type de ressource spécifique
- **Filtrage par date** : Plage de dates personnalisée
- **Onglets rapides** : Aujourd'hui, Cette semaine, par type d'action
- **Rafraîchissement automatique** : La liste se met à jour toutes les 30 secondes
- **Détails complets** : Cliquez sur une ligne pour voir tous les détails d'une action

### Informations enregistrées

Pour chaque action, le système enregistre :

- Date et heure
- Utilisateur (nom et email)
- Type d'action
- Ressource concernée
- Élément modifié
- Anciennes valeurs (pour les modifications et suppressions)
- Nouvelles valeurs (pour les créations et modifications)
- Adresse IP
- User Agent (navigateur)
- URL de la page

## Intégration dans les nouvelles ressources

### 1. Ajouter le trait aux pages CRUD

Pour activer l'audit sur une nouvelle ressource Filament, ajoutez le trait `FilamentAuditTrait` à vos pages CRUD :

```php
<?php

namespace App\Filament\Resources\MyResource\Pages;

use App\Traits\FilamentAuditTrait;
use Filament\Resources\Pages\CreateRecord;

class CreateMyResource extends CreateRecord
{
    use FilamentAuditTrait; // Ajouter cette ligne

    protected static string $resource = MyResourceResource::class;
}
```

Faites de même pour `EditMyResource` et toute autre page CRUD.

### 2. Les pages qui nécessitent le trait

- `CreateRecord` : Pour tracer les créations
- `EditRecord` : Pour tracer les modifications et suppressions

### 3. Exemple complet

```php
// CreateUser.php
use App\Traits\FilamentAuditTrait;

class CreateUser extends CreateRecord
{
    use FilamentAuditTrait;
    protected static string $resource = UserResource::class;
}

// EditUser.php
use App\Traits\FilamentAuditTrait;

class EditUser extends EditRecord
{
    use FilamentAuditTrait;
    protected static string $resource = UserResource::class;
}
```

## Utilisation manuelle du service d'audit

Vous pouvez également enregistrer des actions personnalisées :

```php
use App\Services\AdminAuditService;

// Enregistrer une consultation
AdminAuditService::logView($model, 'Nom de la ressource');

// Enregistrer une création
AdminAuditService::logCreate($model, 'Nom de la ressource');

// Enregistrer une modification
AdminAuditService::logUpdate($model, 'Nom de la ressource', $oldValues, $newValues);

// Enregistrer une suppression
AdminAuditService::logDelete($model, 'Nom de la ressource');

// Enregistrer une action personnalisée
AdminAuditService::log(
    action: 'custom_action',
    resource: $model,
    resourceName: 'Ma Ressource',
    description: 'Description de l\'action',
    metadata: ['key' => 'value']
);
```

## Architecture du système

### Composants principaux

1. **AdminAuditLog** (Model) : Modèle Eloquent pour la table `admin_audit_logs`
2. **AdminAuditService** (Service) : Service pour enregistrer les actions
3. **FilamentAuditMiddleware** (Middleware) : Capture les consultations de pages
4. **FilamentAuditTrait** (Trait) : Capture les opérations CRUD sur les ressources
5. **AdminAuditLogResource** (Filament Resource) : Interface d'affichage dans le panel admin

### Flux de fonctionnement

1. **Consultation de page** : Le middleware `FilamentAuditMiddleware` intercepte les requêtes GET et enregistre la page visitée
2. **Opération CRUD** : Le trait `FilamentAuditTrait` utilise les hooks de Filament (`afterCreate`, `afterSave`, `afterDelete`) pour enregistrer les actions
3. **Stockage** : Toutes les actions sont enregistrées dans la table `admin_audit_logs` via le service `AdminAuditService`
4. **Affichage** : La ressource Filament permet de consulter et filtrer les logs

### Sécurité

- Les champs sensibles (mot de passe, tokens, etc.) sont automatiquement masqués
- Les logs d'audit ne peuvent pas être modifiés ou supprimés depuis l'interface
- L'adresse IP et le User Agent sont enregistrés pour chaque action
- Le nom de l'utilisateur est dupliqué pour conserver l'information même si le compte est supprimé

## Maintenance

### Nettoyage des anciens logs

Pour éviter que la table ne devienne trop volumineuse, vous pouvez créer une commande planifiée pour supprimer les anciens logs :

```php
// Dans app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    // Supprimer les logs de plus de 6 mois
    $schedule->call(function () {
        \App\Models\AdminAuditLog::where('created_at', '<', now()->subMonths(6))->delete();
    })->monthly();
}
```

### Statistiques

Le service fournit des méthodes pour obtenir des statistiques :

```php
use App\Services\AdminAuditService;

// Statistiques pour un utilisateur sur la dernière semaine
$stats = AdminAuditService::getUserStats($userId, 'week');

// Résultat :
// [
//     'total' => 150,
//     'by_action' => ['view' => 100, 'create' => 20, 'update' => 25, 'delete' => 5],
//     'by_resource' => ['Utilisateur' => 50, 'Compte joueur' => 100]
// ]
```

## Migration de la base de données

Pour créer la table des logs d'audit, exécutez :

```bash
php artisan migrate
```

La migration créera la table `admin_audit_logs` avec tous les champs nécessaires et les index pour améliorer les performances.
