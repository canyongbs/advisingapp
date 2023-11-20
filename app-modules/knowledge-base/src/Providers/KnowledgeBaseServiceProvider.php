<?php

namespace Assist\KnowledgeBase\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\KnowledgeBase\KnowledgeBasePlugin;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Assist\Authorization\AuthorizationRoleRegistry;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\KnowledgeBase\Observers\KnowledgeBaseItemObserver;

class KnowledgeBaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new KnowledgeBasePlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'knowledge_base_item' => KnowledgeBaseItem::class,
            'knowledge_base_category' => KnowledgeBaseCategory::class,
            'knowledge_base_quality' => KnowledgeBaseQuality::class,
            'knowledge_base_status' => KnowledgeBaseStatus::class,
        ]);

        $this->registerRolesAndPermissions();
        $this->registerObservers();
    }

    public function registerObservers(): void
    {
        KnowledgeBaseItem::observe(KnowledgeBaseItemObserver::class);
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'knowledge-base',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'knowledge-base',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'knowledge-base',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'knowledge-base',
            path: 'roles/web'
        );
    }
}
