<?php

use App\Features\NotificationSettingsFeature;
use App\Filament\Pages\ManageNotificationSettings;
use App\Settings\NotificationSettings;
use CanyonGBS\Common\Enums\Color;

use function Pest\Laravel\get;
use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

beforeEach(function () {
    NotificationSettingsFeature::activate();
});

it('can render the manage notification settings page', function () {
    asSuperAdmin();

    get(ManageNotificationSettings::getUrl())
        ->assertOk();
});

it('validates the inputs', function (array $state, array $errors) {
    asSuperAdmin();

    livewire(ManageNotificationSettings::class)
        ->fillForm($state)
        ->call('save')
        ->assertHasFormErrors($errors);
})->with([
    'name required' => [['name' => null], ['name' => 'required']],
    'from_name max' => [['from_name' => str_repeat('a', 151)], ['from_name' => 'max']],
]);

it('can update the notification settings', function () {
    asSuperAdmin();

    livewire(ManageNotificationSettings::class)
        ->fillForm([
            'name' => 'New Institution Name',
            'from_name' => 'New From Name',
            'description' => 'New description.',
            'primary_color' => Color::Blue->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(NotificationSettings::class);

    expect($settings->name)->toBe('New Institution Name')
        ->and($settings->from_name)->toBe('New From Name')
        ->and($settings->description)->toBe('New description.')
        ->and($settings->primary_color)->toBe(Color::Blue);
});
