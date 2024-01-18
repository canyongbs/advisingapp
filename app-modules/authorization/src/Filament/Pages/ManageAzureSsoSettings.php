<?php

namespace AdvisingApp\Authorization\Filament\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use App\Filament\Clusters\ProductIntegrations;
use AdvisingApp\Authorization\Settings\AzureSsoSettings;

class ManageAzureSsoSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = AzureSsoSettings::class;

    protected static ?string $title = 'Azure SSO Settings';

    protected static ?string $navigationLabel = 'Azure SSO';

    protected static ?int $navigationSort = 60;

    protected static ?string $cluster = ProductIntegrations::class;

    public static function canAccess(): bool
    {
        return auth()->user()->can('authorization.view_azure_sso_settings');
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
                        TextInput::make('redirect')
                            ->string()
                            ->url()
                            ->required(fn (Get $get) => $get('is_enabled')),
                        TextInput::make('tenant_id')
                            ->label('Tenant ID')
                            ->string()
                            ->required(fn (Get $get) => $get('is_enabled'))
                            ->password()
                            ->revealable(),
                    ])->visible(fn (Get $get) => $get('is_enabled')),
            ]);
    }
}
