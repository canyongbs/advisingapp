<?php

namespace Assist\Webhook\Providers;

use Filament\Panel;
use Aws\Sns\MessageValidator;
use Assist\Webhook\WebhookPlugin;
use Illuminate\Support\ServiceProvider;
use Assist\Webhook\Models\InboundWebhook;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class WebhookServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new WebhookPlugin()));

        $this->app->bind(
            MessageValidator::class,
            fn () => new MessageValidator()
        );
    }

    public function boot(): void
    {
        Relation::morphMap([
            'inbound_webhook' => InboundWebhook::class,
        ]);

        $this->registerRolesAndPermissions();
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'webhook',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'webhook',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'webhook',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'webhook',
            path: 'roles/web'
        );
    }
}
