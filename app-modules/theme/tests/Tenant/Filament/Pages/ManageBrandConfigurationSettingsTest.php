<?php

use AdvisingApp\Theme\Filament\Pages\ManageBrandConfigurationSettings;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

use function Pest\Livewire\livewire;
use function Tests\asSuperAdmin;

it('stores the email-facing logo publicly and keeps browser-only assets private', function () {
    asSuperAdmin();

    livewire(ManageBrandConfigurationSettings::class)
        ->assertFormFieldExists(
            'logo',
            checkFieldUsing: fn (SpatieMediaLibraryFileUpload $field): bool => $field->getDiskName() === 's3-public' && $field->getVisibility() === 'public'
        )
        ->assertFormFieldExists(
            'dark_logo',
            checkFieldUsing: fn (SpatieMediaLibraryFileUpload $field): bool => $field->getDiskName() === 's3' && $field->getVisibility() === 'private'
        )
        ->assertFormFieldExists(
            'favicon',
            checkFieldUsing: fn (SpatieMediaLibraryFileUpload $field): bool => $field->getDiskName() === 's3' && $field->getVisibility() === 'private'
        );
});
