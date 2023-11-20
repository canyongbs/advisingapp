<?php

namespace Assist\IntegrationGoogleAnalytics\Filament\Pages;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Assist\IntegrationGoogleAnalytics\Settings\GoogleAnalyticsSettings;

class ManageGoogleAnalyticsSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = GoogleAnalyticsSettings::class;

    protected static ?string $title = 'Google Analytics Settings';

    protected static ?string $navigationLabel = 'Google Analytics';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 5;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('integration-google-analytics.view_google_analytics_settings');
    }

    public function mount(): void
    {
        $this->authorize('integration-google-analytics.view_google_analytics_settings');

        parent::mount();
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Toggle::make('is_enabled')
                    ->label('Enabled')
                    ->live(),
                TextInput::make('id')
                    ->visible(fn (Get $get) => $get('is_enabled')),
                // TODO: add key value options?
            ]);
    }
}
