<?php

namespace App\Http\Middleware;

use App\Services\AdminAuditService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class FilamentAuditMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (
            $request->isMethod('GET') &&
            $request->user() &&
            Str::startsWith($request->path(), 'admin/')
        ) {
            $this->logPageView($request);
        }

        return $response;
    }

    private function logPageView(Request $request): void
    {
        $path = $request->path();

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

        $pageInfo = $this->detectPageInfo($path);

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

    private function detectPageInfo(string $path): array
    {
        $segments = explode('/', trim($path, '/'));

        if (count($segments) < 2) {
            return [
                'page_type' => 'dashboard',
                'resource_type' => null,
                'resource_name' => 'Tableau de bord',
                'description' => 'a consulté le tableau de bord',
            ];
        }

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

        $pageType = match ($action) {
            'create' => 'create',
            'edit' => 'edit',
            default => 'list',
        };

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
