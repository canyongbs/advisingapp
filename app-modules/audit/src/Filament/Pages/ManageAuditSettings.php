<?php

namespace Assist\Audit\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Select;
use Assist\Audit\Settings\AuditSettings;
use Assist\Audit\Actions\Finders\AuditableModels;

class ManageAuditSettings extends SettingsPage
{
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = AuditSettings::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('audited_models')
                    ->options(AuditableModels::all())
                    ->multiple(),
            ]);
    }
}
