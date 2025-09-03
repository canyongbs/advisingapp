<?php

namespace AdvisingApp\StockMedia\Filament\Pages;

use AdvisingApp\StockMedia\Enums\StockMediaProvider;
use AdvisingApp\StockMedia\Settings\StockMediaSettings;
use App\Features\StockMediaFeature;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\SettingsPage;

class ManageStockMediaSettings extends SettingsPage
{
    protected static ?string $navigationGroup = 'Global Administration';

    protected static ?int $navigationSort = 80;

    protected static ?string $navigationLabel = 'Stock Media Settings';

    protected static string $settings = StockMediaSettings::class;

    protected static ?string $title = 'Stock Media Settings';

    public static function canAccess(): bool
    {
        return StockMediaFeature::active() && parent::canAccess();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Toggle::make('active')
                    ->live(),
                Select::make('provider')
                    ->options(StockMediaProvider::class)
                    ->default('pexels')
                    ->visible(fn(Get $get) => $get('active')),
                //get the correct api key
            ]);
    }
}