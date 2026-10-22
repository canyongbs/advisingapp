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
      of the licensor in the software. Any use of the licensor’s trademarks is subject
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

use AdvisingApp\Application\Database\Seeders\ApplicationSubmissionStateSeeder;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationEmailAutoReply;
use App\Features\AdmissionsAndEventsEmailAutoReplyFeature;

use function Pest\Laravel\seed;

beforeEach(function () {
    seed(ApplicationSubmissionStateSeeder::class);
});

it('creates a disabled email auto reply for a new application', function () {
    expect(ApplicationEmailAutoReply::query()->count())->toBe(0);

    $application = Application::factory()->create();

    $emailAutoReply = $application->emailAutoReply()->firstOrFail();

    expect(ApplicationEmailAutoReply::query()->count())->toBe(1)
        ->and($emailAutoReply->is_enabled)->toBeFalse();
});

it('does not create an email auto reply for a new version of an application', function () {
    $application = Application::factory()->create();

    $newVersion = Application::factory()->create(['root_id' => $application->root_id]);

    expect($newVersion->emailAutoReply()->exists())->toBeFalse()
        ->and(ApplicationEmailAutoReply::query()->count())->toBe(1);
});

it('does not create an email auto reply when the feature is inactive', function () {
    AdmissionsAndEventsEmailAutoReplyFeature::deactivate();

    $application = Application::factory()->create();

    expect($application->emailAutoReply()->exists())->toBeFalse();
});
