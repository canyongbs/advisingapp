<?php

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
