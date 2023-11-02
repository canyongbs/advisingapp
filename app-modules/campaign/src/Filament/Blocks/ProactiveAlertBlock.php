<?php

namespace Assist\Campaign\Filament\Blocks;

use Assist\Alert\Enums\AlertStatus;
use Assist\Alert\Enums\AlertSeverity;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DateTimePicker;

class ProactiveAlertBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Proactive Alert');

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Textarea::make($fieldPrefix . 'description')
                ->required()
                ->string(),
            Select::make($fieldPrefix . 'severity')
                ->options(AlertSeverity::class)
                ->selectablePlaceholder(false)
                ->default(AlertSeverity::default())
                ->required()
                ->enum(AlertSeverity::class),
            Textarea::make($fieldPrefix . 'suggested_intervention')
                ->required()
                ->string(),
            Select::make($fieldPrefix . 'status')
                ->options(AlertStatus::class)
                ->selectablePlaceholder(false)
                ->default(AlertStatus::default())
                ->required()
                ->enum(AlertStatus::class),
            DateTimePicker::make($fieldPrefix . 'execute_at')
                ->label('When should the action be executed?')
                ->required()
                ->minDate(now(auth()->user()->timezone))
                ->closeOnDateSelection(),
        ];
    }

    public static function type(): string
    {
        return 'proactive_alert';
    }
}
