<?php

namespace Assist\IntegrationAI\Providers;

use Filament\Panel;
use Illuminate\Support\ServiceProvider;
use Assist\IntegrationAI\Client\AzureOpenAI;
use Assist\IntegrationAI\IntegrationAIPlugin;
use Assist\Authorization\AuthorizationRoleRegistry;
use Assist\IntegrationAI\Client\Contracts\AIChatClient;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\IntegrationAI\Client\Playground\AzureOpenAI as PlaygroundAzureOpenAI;

class IntegrationAIServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new IntegrationAIPlugin()));

        $this->app->singleton(AIChatClient::class, function () {
            if (config('services.azure_open_ai.enable_test_mode') === true) {
                return new PlaygroundAzureOpenAI();
            }

            return new AzureOpenAI();
        });
    }

    public function boot()
    {
        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'integration-ai',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'integration-ai',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'integration-ai',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'integration-ai',
            path: 'roles/web'
        );
    }
}
