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

namespace Assist\Assistant\Providers;

use Filament\Panel;
use Filament\Support\Assets\Js;
use Assist\Assistant\AssistantPlugin;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Assist\Assistant\Models\AssistantChat;
use Filament\Support\Facades\FilamentAsset;
use Assist\Assistant\Models\AssistantChatFolder;
use Assist\Assistant\Models\AssistantChatMessage;
use Assist\IntegrationAI\Events\AIPromptInitiated;
use Assist\Authorization\AuthorizationRoleRegistry;
use Assist\Assistant\Models\AssistantChatMessageLog;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Assistant\Listeners\LogAssistantChatMessage;
use Assist\Authorization\AuthorizationPermissionRegistry;

class AssistantServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AssistantPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'assistant_chat' => AssistantChat::class,
            'assistant_chat_message' => AssistantChatMessage::class,
            'assistant_chat_message_log' => AssistantChatMessageLog::class,
            'assistant_chat_folder' => AssistantChatFolder::class,
        ]);

        $this->registerEvents();
        $this->registerRolesAndPermissions();
        $this->registerAssets();
    }

    public function registerAssets(): void
    {
        FilamentAsset::register([
            Js::make('assistantCurrentResponse', __DIR__ . '/../../resources/js/dist/assistantCurrentResponse.js')->loadedOnRequest(),
        ], 'canyon-gbs/assistant');
    }

    protected function registerEvents(): void
    {
        Event::listen(AIPromptInitiated::class, LogAssistantChatMessage::class);
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'assistant',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'assistant',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'assistant',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'assistant',
            path: 'roles/web'
        );
    }
}
