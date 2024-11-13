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

namespace AdvisingApp\Alert\Providers;

use Filament\Panel;
use AdvisingApp\Alert\AlertPlugin;
use AdvisingApp\Alert\Models\Alert;
use App\Concerns\ImplementsGraphQL;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use AdvisingApp\Alert\Models\AlertStatus;
use AdvisingApp\Alert\Enums\AlertSeverity;
use AdvisingApp\Alert\Events\AlertCreated;
use AdvisingApp\Alert\Histories\AlertHistory;
use AdvisingApp\Alert\Observers\AlertObserver;
use AdvisingApp\Alert\Observers\AlertStatusObserver;
use Illuminate\Database\Eloquent\Relations\Relation;
use AdvisingApp\Alert\Observers\AlertHistoryObserver;
use AdvisingApp\Alert\Enums\SystemAlertStatusClassification;
use AdvisingApp\Alert\Listeners\NotifySubscribersOfAlertCreated;

class AlertServiceProvider extends ServiceProvider
{
  use ImplementsGraphQL;

  public function register(): void
  {
    Panel::configureUsing(fn(Panel $panel) => ($panel->getId() !== 'admin') || $panel->plugin(new AlertPlugin()));
  }

  public function boot(): void
  {
    Relation::morphMap([
      'alert' => Alert::class,
      'alert_history' => AlertHistory::class,
    ]);

    $this->registerObservers();

    $this->registerEvents();

    $this->registerGraphQL();
  }

  protected function registerObservers(): void
  {
    Alert::observe(AlertObserver::class);
    AlertHistory::observe(AlertHistoryObserver::class);
    AlertStatus::observe(AlertStatusObserver::class);
  }

  protected function registerEvents(): void
  {
    Event::listen(
      AlertCreated::class,
      NotifySubscribersOfAlertCreated::class
    );
  }

  protected function registerGraphQL(): void
  {
    $this->discoverSchema(__DIR__ . '/../../graphql/alert.graphql');

    $this->registerEnum(AlertSeverity::class);
    $this->registerEnum(SystemAlertStatusClassification::class);
  }
}
