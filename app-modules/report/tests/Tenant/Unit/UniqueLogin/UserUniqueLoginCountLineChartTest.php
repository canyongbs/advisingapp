<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Report\Enums\TrackedEventType;
use AdvisingApp\Report\Filament\Widgets\UserUniqueLoginCountLineChart;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach()->skip('Skipping these tests as there are currently issues with these tests or the underlying functionality having to do with overflow dates that needs to be resolved');

it('checks users with tracked_event_type unique-login count in line chart', function () {
    User::factory()->count(5)->hasLogins(['type' => TrackedEventType::UserLogin, 'occurred_at' => now()->subMonths(1)])->create();
    User::factory()->count(3)->hasLogins(['type' => TrackedEventType::UserLogin, 'occurred_at' => now()->subMonths(6)])->create();

    $widgetInstance = livewire(UserUniqueLoginCountLineChart::class, ['cacheTag' => 'report-users'])->instance();
    $invadedWidget = invade($widgetInstance);

    expect($invadedWidget->getData()['datasets'][0]['data'])->toMatchSnapshot();
});
