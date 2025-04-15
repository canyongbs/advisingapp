<?php

namespace App\Filament\Pages;

use App\Features\ImportSettingsFeature;
use App\Filament\Clusters\ProductIntegrations;
use App\Models\User;
use App\Settings\ImportSettings;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Squire\Models\Country;

class ManageImportSettings extends SettingsPage
{
    protected static ?string $title = 'Sync and Imports';

    protected static ?int $navigationSort = 120;

    protected static string $settings = ImportSettings::class;

    protected static ?string $cluster = ProductIntegrations::class;

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return ImportSettingsFeature::active() && $user->isSuperAdmin();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('default_country')
                    ->label('Default country code')
                    ->options(fn (): array => Country::query()
                        ->orderBy('name')
                        ->select(['id', 'name', 'calling_code'])
                        ->get()
                        ->mapWithKeys(fn (Country $country): array => [$country->getKey() => "{$country->name} (+{$country->calling_code})"])
                        ->all())
                    ->required(),
            ]);
    }
}
