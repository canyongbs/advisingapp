<?php

namespace AdvisingApp\Theme\Filament\Pages;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use App\Filament\Clusters\GlobalSettings;
use App\Settings\CollegeBrandingSettings;
use App\Filament\Forms\Components\ColorSelect;

class ManageCollegeBrandingSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'College Branding';

    protected static ?int $navigationSort = 90;

    protected static string $settings = CollegeBrandingSettings::class;

    protected static ?string $title = 'College Branding';

    protected static ?string $cluster = GlobalSettings::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can('theme.manage_college_brand_settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('is_enabled')
                    ->inline(false)
                    ->label('Enable Branding Bar')
                    ->required()
                    ->live()
                    ->columnSpanFull(),
                TextInput::make('college_text')
                    ->label('College Text')
                    ->placeholder('@ Canyon Community College - Go Lions!')
                    ->required()
                    ->string()
                    ->visible(fn (Get $get) => $get('is_enabled'))
                    ->autocomplete(false),
                ColorSelect::make('color')
                    ->label('Branding Bar Color')
                    ->visible(fn (Get $get) => $get('is_enabled'))
                    ->required(),
            ]);
    }

    public function getRedirectUrl(): ?string
    {
        return ManageCollegeBrandingSettings::getUrl();
    }
}
