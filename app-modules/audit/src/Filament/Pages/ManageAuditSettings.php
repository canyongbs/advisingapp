<?php

namespace Assist\Audit\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Select;
use Assist\Audit\Settings\AuditSettings;
use Filament\Forms\Components\TextInput;
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
                    ->multiple()
                    ->in(AuditableModels::all()->keys()->toArray())
                    ->rules(
                        [
                            'array',
                        ]
                    )
                    ->hintIcon(
                        icon: 'heroicon-m-question-mark-circle',
                        tooltip: 'Items added here will be tracked by the audit trail.'
                    ),
                TextInput::make('retention_duration')
                    ->integer()
                    ->minValue(1)
                    ->step(1)
                    ->suffix('Day/s')
                    ->hintIcon(
                        icon: 'heroicon-m-question-mark-circle',
                        tooltip: 'Audit trail records older than the retention duration will be deleted.'
                    ),
            ]);
    }
}
