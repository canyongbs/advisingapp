<?php

namespace AdvisingApp\IntegrationAwsSesEventHandling\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use AdvisingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;

class ManageAmazonSesSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = SesSettings::class;

    protected static ?string $title = 'Amazon SES Settings';

    protected static ?string $navigationLabel = 'Amazon SES';

    protected static ?string $navigationGroup = 'Integrations';

    protected static ?int $navigationSort = 50;

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('integration-aws-ses-event-handling.view_ses_settings');
    }

    public function mount(): void
    {
        $this->authorize('integration-aws-ses-event-handling.view_ses_settings');

        parent::mount();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('configuration_set')
                            ->label('Configuration Set'),
                    ]),
            ]);
    }
}
