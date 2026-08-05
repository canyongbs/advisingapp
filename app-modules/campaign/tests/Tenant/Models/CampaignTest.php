<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor's trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Tests\Tenant\Models;

use AdvisingApp\Campaign\Models\Campaign;

use function Tests\asSuperAdmin;

it('disables an enabled campaign when it is archived', function () {
    asSuperAdmin();

    $campaign = Campaign::factory()->enabled()->create();

    // Establish starting state before the side effect.
    expect($campaign->enabled)->toBeTrue()
        ->and($campaign->archived_at)->toBeNull();

    $campaign->archive();

    $campaign->refresh();

    expect($campaign->enabled)->toBeFalse()
        ->and($campaign->archived_at)->not->toBeNull();
});

it('archives an already disabled campaign without re-enabling it', function () {
    asSuperAdmin();

    $campaign = Campaign::factory()->disabled()->create();

    expect($campaign->enabled)->toBeFalse()
        ->and($campaign->archived_at)->toBeNull();

    $campaign->archive();

    $campaign->refresh();

    expect($campaign->enabled)->toBeFalse()
        ->and($campaign->archived_at)->not->toBeNull();
});
