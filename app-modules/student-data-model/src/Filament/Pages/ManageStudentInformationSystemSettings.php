<?php

namespace AdvisingApp\StudentDataModel\Filament\Pages;

use Filament\Forms\Get;
use Filament\Forms\Form;
use App\Enums\FeatureFlag;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use App\Filament\Clusters\GlobalSettings;
use AdvisingApp\StudentDataModel\Enums\SisSystem;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;

class ManageStudentInformationSystemSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $title = 'Student Information System';

    protected static string $settings = StudentInformationSystemSettings::class;

    protected static ?string $cluster = GlobalSettings::class;

    protected static ?string $navigationGroup = 'Product Integrations';

    protected static ?int $navigationSort = 110;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return FeatureFlag::SisIntegrationSettings->active() && $user->can('sis.manage_sis_settings');
    }

    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                Toggle::make('is_enabled')
                    ->live(),
                Section::make()
                    ->schema([
                        Select::make('sis_system')
                            ->label('SIS System')
                            ->options(SisSystem::class)
                            ->enum(SisSystem::class)
                            ->required()
                            ->dehydrateStateUsing(fn (string $state) => SisSystem::from($state)),
                    ])
                    ->visible(fn (Get $get) => $get('is_enabled')),
            ]);
    }
}
