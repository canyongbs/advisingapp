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

namespace Assist\Form\Providers;

use Filament\Panel;
use Assist\Form\FormPlugin;
use Assist\Form\Models\Form;
use Assist\Form\Models\FormField;
use Illuminate\Support\Facades\Event;
use Assist\Form\Models\FormSubmission;
use Illuminate\Support\ServiceProvider;
use Assist\Form\Events\FormSubmissionCreated;
use Assist\Form\Observers\FormSubmissionObserver;
use Assist\Authorization\AuthorizationRoleRegistry;
use Illuminate\Database\Eloquent\Relations\Relation;
use Assist\Authorization\AuthorizationPermissionRegistry;
use Assist\Form\Listeners\NotifySubscribersOfFormSubmission;

class FormServiceProvider extends ServiceProvider
{
    public function register()
    {
        Panel::configureUsing(fn (Panel $panel) => $panel->plugin(new FormPlugin()));
    }

    public function boot()
    {
        Relation::morphMap([
            'form' => Form::class,
            'form_field' => FormField::class,
            'form_submission' => FormSubmission::class,
        ]);

        $this->registerRolesAndPermissions();
        $this->registerObservers();
        $this->registerEvents();
    }

    public function registerObservers(): void
    {
        FormSubmission::observe(FormSubmissionObserver::class);
    }

    public function registerEvents(): void
    {
        Event::listen(
            events: FormSubmissionCreated::class,
            listener: NotifySubscribersOfFormSubmission::class
        );
    }

    protected function registerRolesAndPermissions(): void
    {
        $permissionRegistry = app(AuthorizationPermissionRegistry::class);

        $permissionRegistry->registerApiPermissions(
            module: 'form',
            path: 'permissions/api/custom'
        );

        $permissionRegistry->registerWebPermissions(
            module: 'form',
            path: 'permissions/web/custom'
        );

        $roleRegistry = app(AuthorizationRoleRegistry::class);

        $roleRegistry->registerApiRoles(
            module: 'form',
            path: 'roles/api'
        );

        $roleRegistry->registerWebRoles(
            module: 'form',
            path: 'roles/web'
        );
    }
}
