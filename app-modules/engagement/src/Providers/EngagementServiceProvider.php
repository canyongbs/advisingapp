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

namespace AdvisingApp\Engagement\Providers;

use Filament\Panel;
use App\Models\Tenant;
use App\Concerns\ImplementsGraphQL;
use App\Models\Scopes\SetupIsComplete;
use Illuminate\Support\ServiceProvider;
use Illuminate\Console\Scheduling\Schedule;
use AdvisingApp\Engagement\EngagementPlugin;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\SmsTemplate;
use AdvisingApp\Engagement\Models\EmailTemplate;
use AdvisingApp\Engagement\Models\EngagementFile;
use AdvisingApp\Engagement\Models\EngagementBatch;
use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Engagement\Actions\DeliverEngagements;
use AdvisingApp\Engagement\Models\EngagementDeliverable;
use AdvisingApp\Engagement\Enums\EngagementDeliveryMethod;
use AdvisingApp\Engagement\Enums\EngagementDeliveryStatus;

class EngagementServiceProvider extends ServiceProvider
{
    use ImplementsGraphQL;

    public function register(): void
    {
        Panel::configureUsing(fn (Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new EngagementPlugin()));
    }

    public function boot(): void
    {
        Relation::morphMap([
            'email_template' => EmailTemplate::class,
            'engagement_batch' => EngagementBatch::class,
            'engagement_deliverable' => EngagementDeliverable::class,
            'engagement_file' => EngagementFile::class,
            'engagement_response' => EngagementResponse::class,
            'engagement' => Engagement::class,
            'sms_template' => SmsTemplate::class,
        ]);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule) {
            $schedule->call(function () {
                Tenant::query()
                    ->tap(new SetupIsComplete())
                    ->cursor()
                    ->each(function (Tenant $tenant) {
                        $tenant->execute(function () {
                            dispatch(new DeliverEngagements());
                        });
                    });
            })
                ->everyMinute()
                ->name('DeliverEngagementsSchedule')
                ->onOneServer()
                ->withoutOverlapping();
        });

        $this->registerGraphQL();
    }

    protected function registerGraphQL(): void
    {
        $this->discoverSchema(__DIR__ . '/../../graphql/*');

        $this->registerEnum(EngagementDeliveryMethod::class);
        $this->registerEnum(EngagementDeliveryStatus::class);
    }
}
