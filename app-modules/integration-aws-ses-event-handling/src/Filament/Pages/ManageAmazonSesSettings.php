<?php

namespace AdvisingApp\IntegrationAwsSesEventHandling\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use AdvisingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;

class ManageAmazonSesSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = SesSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('key'),
                        TextInput::make('secret'),
                        TextInput::make('region'),
                        TextInput::make('configuration_set'),
                    ]),
            ]);
    }
}
