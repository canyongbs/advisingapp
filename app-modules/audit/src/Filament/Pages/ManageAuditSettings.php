<?php

namespace Assist\Audit\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Select;
use Assist\Audit\Settings\AuditSettings;
use Filament\Forms\Components\TextInput;
use Assist\Audit\Actions\Finders\AuditableModels;
use App\Filament\Pages\Concerns\HasNavigationGroup;

class ManageAuditSettings extends SettingsPage
{
    use HasNavigationGroup;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationLabel = 'Audit Configuration';

    protected static string $settings = AuditSettings::class;

    protected static ?string $title = 'Audit Configuration';

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
                    )
                    ->columnSpanFull(),
                TextInput::make('retention_duration_in_days')
                    ->label('Retention Duration')
                    ->integer()
                    ->minValue(1)
                    ->step(1)
                    ->suffix('Day/s')
                    ->hintIcon(
                        icon: 'heroicon-m-question-mark-circle',
                        tooltip: 'Audit trail records older than the retention duration will be deleted.'
                    ),
                TextInput::make('assistant_chat_message_logs_retention_duration_in_days')
                    ->label('Assistant retention Duration')
                    ->integer()
                    ->minValue(1)
                    ->step(1)
                    ->suffix('Day/s')
                    ->hintIcon(
                        icon: 'heroicon-m-question-mark-circle',
                        tooltip: 'Assistant chat message logs older than the retention duration will be deleted.'
                    ),
            ]);
    }
}
