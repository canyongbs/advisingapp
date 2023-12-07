<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Form\Providers;

use Filament\Panel;
use Assist\Form\FormPlugin;
use Assist\Form\Models\Form;
use Assist\Form\Models\FormField;
use Assist\Form\Models\FormRequest;
use Illuminate\Support\Facades\Event;
use Assist\Form\Models\FormSubmission;
use Illuminate\Support\ServiceProvider;
use Assist\Form\Events\FormSubmissionCreated;
use Assist\Form\Observers\FormRequestObserver;
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
            'form_request' => FormRequest::class,
            'form_submission' => FormSubmission::class,
        ]);

        $this->registerRolesAndPermissions();
        $this->registerObservers();
        $this->registerEvents();
    }

    public function registerObservers(): void
    {
        FormRequest::observe(FormRequestObserver::class);
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
