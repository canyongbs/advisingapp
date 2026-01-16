<?php

namespace App\Filament\Pages;

use App\Features\InstitutionDetailsSettingsFeature;
use App\Filament\Clusters\DisplaySettings;
use App\Models\User;
use App\Settings\InstitutionDetailsSettings;
use BackedEnum;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageInstitutionDetailsSettings extends SettingsPage
{
    protected static ?string $navigationLabel = 'Profile';

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = DisplaySettings::class;

    protected static string $settings = InstitutionDetailsSettings::class;

    public static function canAccess(): bool
    {
        $user = auth()->user();
        assert($user instanceof User);

        return InstitutionDetailsSettingsFeature::active() && $user->can(['settings.view-any']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ipeds_id')
                    ->label('IPEDS ID')
                    ->maxLength(255),
                TextInput::make('name')
                    ->maxLength(255),
                SpatieMediaLibraryFileUpload::make('dark_logo')
                    ->label('Dark Logo')
                    ->disk('s3')
                    ->collection('dark_logo')
                    ->visibility('private')
                    ->image()
                    ->maxSize(10240)
                    ->model(
                        InstitutionDetailsSettings::getSettingsPropertyModel('institution.dark_logo'),
                    )
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('light_logo')
                    ->disk('s3')
                    ->collection('light_logo')
                    ->visibility('private')
                    ->image()
                    ->maxSize(10240)
                    ->model(
                        InstitutionDetailsSettings::getSettingsPropertyModel('institution.light_logo'),
                    )
                    ->columnSpanFull(),
            ]);
    }
}
