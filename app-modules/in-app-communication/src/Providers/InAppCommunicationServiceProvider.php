<?php

namespace Assist\InAppCommunication\Providers;

use Filament\Panel;
use Filament\Support\Assets\Js;
use Illuminate\Support\ServiceProvider;
use Filament\Support\Facades\FilamentAsset;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\InAppCommunication\InAppCommunicationPlugin;
use Assist\InAppCommunication\Models\TwilioConversation;
use Assist\Authorization\AuthorizationPermissionRegistry;

class InAppCommunicationServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new InAppCommunicationPlugin()));
    }

    public function boot()
    {
        Relation::morphMap(
            [
                'twilio_conversation' => TwilioConversation::class,
            ]
        );

        $this->registerRolesAndPermissions();
        $this->registerAssets();
    }

    public function registerAssets(): void
    {
        FilamentAsset::register([
            Js::make('userToUserChat', __DIR__ . '/../../resources/js/dist/userToUserChat.js')->loadedOnRequest(),
        ], 'canyon-gbs/in-app-communication');
    }

    protected function registerRolesAndPermissions()
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'in-app-communication',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'in-app-communication',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'in-app-communication',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'in-app-communication',
            path: 'roles/web'
        );
    }
}
