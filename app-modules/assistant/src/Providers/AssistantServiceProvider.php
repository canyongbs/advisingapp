<?php

namespace Assist\Assistant\Providers;

use Filament\Panel;
use Assist\Assistant\AssistantPlugin;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Assist\Assistant\Models\AssistantChat;
use Assist\Assistant\Models\AssistantChatMessage;
use Assist\IntegrationAI\Events\AIPromptInitiated;
use Assist\Authorization\AuthorizationRoleRegistry;
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
        ]);

        $this->registerEvents();
        $this->registerRolesAndPermissions();
    }

    protected function registerEvents(): void
    {
        Event::listen(AIPromptInitiated::class, LogAssistantChatMessage::class);
    }

    protected function registerRolesAndPermissions()
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
