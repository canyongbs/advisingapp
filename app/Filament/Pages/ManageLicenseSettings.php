<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\LicenseSettings;
use Filament\Forms\Components\TextInput;

class ManageLicenseSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = LicenseSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('license_key')
                    ->label('License Key')
                    ->required()
                    ->disabled(),
            ]);
    }
}
