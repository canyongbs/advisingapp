<?php

use AdvisingApp\Ai\Models\AiAssistant;
use AdvisingApp\Authorization\Enums\LicenseType;
use App\Models\User;
use App\Settings\LicenseSettings;

use function Tests\asSuperAdmin;

test('viewAny', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

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
    $this->actingAs($user);

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
    $this->actingAs($user);

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
    $this->actingAs($user);

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