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

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Providers;

use Filament\Panel;
use App\Models\Tenant;
use Livewire\Livewire;
use App\Models\Scopes\SetupIsComplete;
use Illuminate\Support\ServiceProvider;
use AdvisingApp\MeetingCenter\Models\Event;
use Illuminate\Console\Scheduling\Schedule;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Jobs\SyncCalendars;
use AdvisingApp\MeetingCenter\MeetingCenterPlugin;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormSubmission;
use AdvisingApp\MeetingCenter\Livewire\EventAttendeeSubmissionsManager;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormAuthentication;

class MeetingCenterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new MeetingCenterPlugin()));

        app('config')->set('meeting-center', require base_path('app-modules/meeting-center/config/meeting-center.php'));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'calendar' => Calendar::class,
            'calendar_event' => CalendarEvent::class,
            'event' => Event::class,
            'event_attendee' => EventAttendee::class,
            'event_registration_form' => EventRegistrationForm::class,
            'event_registration_form_authentication' => EventRegistrationFormAuthentication::class,
            'event_registration_form_field' => EventRegistrationFormField::class,
            'event_registration_form_step' => EventRegistrationFormStep::class,
            'event_registration_form_submission' => EventRegistrationFormSubmission::class,
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->call(function () {
                Tenant::query()
                    ->tap(new SetupIsComplete())
                    ->cursor()
                    ->each(function (Tenant $tenant) {
                        $tenant->execute(function () {
                            dispatch(new SyncCalendars());
                        });
                    });
            })
                ->everyMinute()
                ->name('SyncCalendarsSchedule')
                ->onOneServer()
                ->withoutOverlapping();
        });

        Livewire::component('event-attendee-submissions-manager', EventAttendeeSubmissionsManager::class);
    }
}
