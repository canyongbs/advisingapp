<?php

namespace Assist\Theme\Filament\Pages;

use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\SettingsProperty;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Assist\Theme\Settings\ThemeSettings;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ManageBrandConfigurationSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Brand Configuration';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 3;

    protected static string $settings = ThemeSettings::class;

    protected static ?string $title = 'Brand Configuration';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Logo')
                    ->aside()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->disk('s3')
                            ->collection('logo')
                            ->visibility('private')
                            ->image()
                            ->model(
                                SettingsProperty::getInstance('theme.is_logo_active'),
                            )
                            ->afterStateUpdated(fn (Set $set) => $set('is_logo_active', true))
                            ->deleteUploadedFileUsing(fn (Set $set) => $set('is_logo_active', false))
                            ->hiddenLabel(),
                        Toggle::make('is_logo_active')
                            ->label('Active')
                            ->disabled(fn (Get $get): bool => blank($get('logo')))
                            ->hint(fn (Get $get): ?string => blank($get('logo')) ? 'Please upload a logo to activate it.' : null)
                            ->dehydrated()
                            ->dehydrateStateUsing(function (Get $get, $state) {
                                if (! filled($get('logo'))) {
                                    return false;
                                }

                                return $state;
                            }),
                    ]),
            ]);
    }

    public function getRedirectUrl(): ?string
    {
        // After saving, redirect to the current page to refresh
        // the logo preview in the layout.
        return ManageBrandConfigurationSettings::getUrl();
    }
}
