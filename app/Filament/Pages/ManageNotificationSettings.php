<?php

namespace App\Filament\Pages;

use App\Features\NotificationSettingsFeature;
use App\Filament\Clusters\Communication;
use App\Filament\Clusters\CommunicationNavigationGroup;
use App\Settings\NotificationSettings;
use BackedEnum;
use CanyonGBS\Common\Enums\Color;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Override;
use UnitEnum;

class ManageNotificationSettings extends SettingsPage
{
    protected static string $settings = NotificationSettings::class;

    protected static ?string $cluster = Communication::class;

    protected static string | UnitEnum | null $navigationGroup = CommunicationNavigationGroup::Settings;

    protected static string | null $navigationLabel = 'Notifications';

    protected static ?int $navigationSort = 120;

    public static function canAccess(): bool
    {
        return NotificationSettingsFeature::active() && parent::canAccess();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('name')
                    ->string()
                    ->required()
                    ->autocomplete(false),
                TextInput::make('from_name')
                    ->string()
                    ->maxLength(150)
                    ->autocomplete(false),
                TextInput::make('description')
                    ->string(),
                ColorSelect::make('primary_color'),
                SpatieMediaLibraryFileUpload::make('logo')
                    ->disk('s3')
                    ->collection('logo')
                    ->visibility('private')
                    ->image(),
            ]);
    }
}
