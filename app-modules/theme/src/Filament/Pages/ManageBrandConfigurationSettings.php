<?php

namespace Assist\Theme\Filament\Pages;

use App\Models\User;
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

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('theme.view_theme_settings');
    }

    public function mount(): void
    {
        $this->authorize('theme.view_theme_settings');

        parent::mount();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Favicon')
                    ->aside()
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('favicon')
                            ->disk('s3')
                            ->collection('favicon')
                            ->visibility('private')
                            ->image()
                            ->model(
                                SettingsProperty::getInstance('theme.is_favicon_active'),
                            )
                            ->afterStateUpdated(fn (Set $set) => $set('is_favicon_active', true))
                            ->deleteUploadedFileUsing(fn (Set $set) => $set('is_favicon_active', false))
                            ->hiddenLabel(),
                        Toggle::make('is_favicon_active')
                            ->label('Active')
                            ->hidden(fn (Get $get): bool => blank($get('favicon'))),
                    ]),
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
                        SpatieMediaLibraryFileUpload::make('dark_logo')
                            ->disk('s3')
                            ->collection('dark_logo')
                            ->visibility('private')
                            ->image()
                            ->model(
                                SettingsProperty::getInstance('theme.is_logo_active'),
                            )
                            ->hidden(fn (Get $get): bool => blank($get('logo'))),
                        Toggle::make('is_logo_active')
                            ->label('Active')
                            ->hidden(fn (Get $get): bool => blank($get('logo'))),
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
