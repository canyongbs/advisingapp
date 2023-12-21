<?php

namespace AdvisingApp\IntegrationTwilio\Filament\Pages;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;

class ManageTwilioSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = TwilioSettings::class;

    protected static ?string $title = 'Twilio Settings';

    protected static ?string $navigationLabel = 'Twilio';

    protected static ?string $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 40;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('integration-twilio.view_twilio_settings');
    }

    public function mount(): void
    {
        $this->authorize('integration-twilio.view_twilio_settings');

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
                Section::make()
                    ->schema([
                        TextInput::make('account_sid')
                            ->string()
                            ->required(),
                        TextInput::make('auth_token')
                            ->string()
                            ->required(),
                        TextInput::make('from_number')
                            ->string()
                            ->required(),
                    ])->visible(fn (Get $get) => $get('is_enabled')),
            ]);
    }
}
