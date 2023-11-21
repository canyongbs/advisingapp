<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

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
