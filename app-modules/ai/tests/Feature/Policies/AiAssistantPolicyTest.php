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

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Tests\asSuperAdmin;

use App\Settings\LicenseSettings;
use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Authorization\Enums\LicenseType;

test('viewAny', function () {
    $user = User::factory()->create();
    actingAs($user);

    expect($user->can('viewAny', AiAssistant::class))->toBeFalse();

    $user->grantLicense(LicenseType::ConversationalAi);
    $user->refresh();

    expect($user->can('viewAny', AiAssistant::class))->toBeFalse();

    $settings = app(LicenseSettings::class);

    $settings->data->addons->customAiAssistants = true;

    $settings->save();

    expect($user->can('viewAny', AiAssistant::class))->toBeFalse();

    $user->givePermissionTo('ai_assistant.view-any');
    $user->refresh();

    expect($user->can('viewAny', AiAssistant::class))->toBeTrue();
});

test('view', function () {
    $user = User::factory()->create();
    actingAs($user);

    $aiAssistant = AiAssistant::factory()->create();

    expect($user->can('view', $aiAssistant))->toBeFalse();

    $user->grantLicense(LicenseType::ConversationalAi);
    $user->refresh();

    expect($user->can('view', $aiAssistant))->toBeFalse();

    $user->givePermissionTo('ai_assistant.*.view');
    $user->refresh();

    expect($user->can('view', $aiAssistant))->toBeTrue();
});

test('create', function () {
    $settings = app(LicenseSettings::class);

    $settings->data->limits->conversationalAiAssistants = 0;

    $settings->save();

    $user = User::factory()->create();
    actingAs($user);

    expect($user->can('create', AiAssistant::class))->toBeFalse();

    $user->grantLicense(LicenseType::ConversationalAi);
    $user->refresh();

    expect($user->can('create', AiAssistant::class))->toBeFalse();

    $user->givePermissionTo('ai_assistant.create');
    $user->refresh();

    expect($user->can('create', AiAssistant::class))->toBeFalse();

    $settings->data->limits->conversationalAiAssistants = 1;

    $settings->save();

    expect($user->can('create', AiAssistant::class))->toBeTrue();
});

test('update', function () {
    $user = User::factory()->create();
    actingAs($user);

    $aiAssistant = AiAssistant::factory()->create();

    expect($user->can('update', $aiAssistant))->toBeFalse();

    $user->grantLicense(LicenseType::ConversationalAi);
    $user->refresh();

    expect($user->can('update', $aiAssistant))->toBeFalse();

    $user->givePermissionTo('ai_assistant.*.update');
    $user->refresh();

    expect($user->can('update', $aiAssistant))->toBeTrue();
});

test('delete', function () {
    asSuperAdmin();

    /** @var User $user */
    $user = auth()->user();

    $aiAssistant = AiAssistant::factory()->create();

    expect($user->can('delete', $aiAssistant))->toBeFalse();
});

test('restore', function () {
    asSuperAdmin();

    /** @var User $user */
    $user = auth()->user();

    $aiAssistant = AiAssistant::factory()->create();

    expect($user->can('restore', $aiAssistant))->toBeFalse();
});

test('forceDelete', function () {
    asSuperAdmin();

    /** @var User $user */
    $user = auth()->user();

    $aiAssistant = AiAssistant::factory()->create();

    expect($user->can('forceDelete', $aiAssistant))->toBeFalse();
});
