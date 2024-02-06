<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Assistant\Providers;

use Filament\Panel;
use Filament\Support\Assets\Js;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use AdvisingApp\Assistant\Models\Prompt;
use AdvisingApp\Assistant\AssistantPlugin;
use Filament\Support\Facades\FilamentAsset;
use AdvisingApp\Assistant\Models\PromptType;
use AdvisingApp\Assistant\Models\AssistantChat;
use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\Assistant\Models\AssistantChatFolder;
use AdvisingApp\Assistant\Models\AssistantChatMessage;
use AdvisingApp\IntegrationAI\Events\AIPromptInitiated;
use AdvisingApp\Authorization\AuthorizationRoleRegistry;
use AdvisingApp\Assistant\Models\AssistantChatMessageLog;
use AdvisingApp\Assistant\Listeners\LogAssistantChatMessage;
use AdvisingApp\Authorization\AuthorizationPermissionRegistry;

class AssistantServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new AssistantPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'assistant_chat' => AssistantChat::class,
            'assistant_chat_message' => AssistantChatMessage::class,
            'assistant_chat_message_log' => AssistantChatMessageLog::class,
            'assistant_chat_folder' => AssistantChatFolder::class,
            'prompt_type' => PromptType::class,
            'prompt' => Prompt::class,
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
