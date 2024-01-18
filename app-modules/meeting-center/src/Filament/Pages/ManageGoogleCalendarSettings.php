<?php

namespace AdvisingApp\MeetingCenter\Filament\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Filament\Clusters\ProductIntegrations;
use AdvisingApp\MeetingCenter\Settings\GoogleCalendarSettings;

class ManageGoogleCalendarSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GoogleCalendarSettings::class;

    protected static ?string $title = 'Google Calendar Settings';

    protected static ?string $navigationLabel = 'Google Calendar';

    protected static ?int $navigationSort = 90;

    protected static ?string $cluster = ProductIntegrations::class;

    public static function canAccess(): bool
    {
        return auth()->user()->can('authorization.view_google_calendar_settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Toggle::make('is_enabled')
                    ->label('Enabled')
                    ->live(),
                Section::make()
                    ->schema([
                        TextInput::make('client_id')
                            ->label('Client ID')
                            ->string()
                            ->required(fn (Get $get) => $get('is_enabled'))
                            ->password()
                            ->revealable(),
                        TextInput::make('client_secret')
                            ->string()
                            ->required(fn (Get $get) => $get('is_enabled'))
                            ->password()
                            ->revealable(),
                    ])->visible(fn (Get $get) => $get('is_enabled')),
            ]);
    }
}
