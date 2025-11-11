<?php

namespace App\Http\Middleware;

use App\Services\AdminAuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class FilamentAuditMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Enregistrer uniquement pour les requêtes GET authentifiées dans le panel admin
        if (
            $request->isMethod('GET') &&
            $request->user() &&
            Str::startsWith($request->path(), 'admin/')
        ) {
            $this->logPageView($request);
        }

        return $response;
    }

    /**
     * Enregistrer la consultation d'une page
     */
    private function logPageView(Request $request): void
    {
        $path = $request->path();

        // Ignorer certaines routes (assets, API, etc.)
        $ignoredPatterns = [
            'admin/livewire',
            'admin/_debugbar',
            'admin/css',
            'admin/js',
            'admin/fonts',
            'admin/images',
        ];

        foreach ($ignoredPatterns as $pattern) {
            if (Str::contains($path, $pattern)) {
                return;
            }
        }

        // Détecter le type de page et la ressource
        $pageInfo = $this->detectPageInfo($path);

        // Enregistrer la consultation
        AdminAuditService::log(
            action: 'view',
            resourceType: $pageInfo['resource_type'],
            resourceName: $pageInfo['resource_name'],
            description: $pageInfo['description'],
            metadata: [
                'page_type' => $pageInfo['page_type'],
                'path' => $path,
            ]
        );
    }

    /**
     * Détecter les informations sur la page consultée
     */
    private function detectPageInfo(string $path): array
    {
        // Pattern: admin/resource-name/action/id
        $segments = explode('/', trim($path, '/'));

        if (count($segments) < 2) {
            return [
                'page_type' => 'dashboard',
                'resource_type' => null,
                'resource_name' => 'Tableau de bord',
                'description' => 'a consulté le tableau de bord',
            ];
        }

        // Mapping des ressources
        $resourceMap = [
            'users' => 'Utilisateur',
            'player-accounts' => 'Compte joueur',
            'forum-categories' => 'Catégorie forum',
            'forum-threads' => 'Discussion forum',
            'forum-posts' => 'Message forum',
            'admin-audit-logs' => 'Trace d\'audit',
        ];

        $resourceSlug = $segments[1] ?? null;
        $action = $segments[2] ?? 'list';
        $id = $segments[3] ?? null;

        $resourceName = $resourceMap[$resourceSlug] ?? ucfirst(str_replace('-', ' ', $resourceSlug));

        // Déterminer le type de page
        $pageType = match ($action) {
            'create' => 'create',
            'edit' => 'edit',
            default => 'list',
        };

        // Générer la description
        $description = match ($pageType) {
            'create' => "a consulté le formulaire de création d'un(e) {$resourceName}",
            'edit' => "a consulté le formulaire d'édition d'un(e) {$resourceName}" . ($id ? " (#{$id})" : ''),
            'list' => "a consulté la liste des {$resourceName}s",
            default => "a consulté la page {$resourceName}",
        };

        return [
            'page_type' => $pageType,
            'resource_type' => null,
            'resource_name' => $resourceName,
            'description' => $description,
        ];
    }
}
