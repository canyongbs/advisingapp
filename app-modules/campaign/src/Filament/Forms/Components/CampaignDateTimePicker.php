<?php

namespace AdvisingApp\Campaign\Filament\Forms\Components;

use AdvisingApp\Campaign\Settings\CampaignSettings;
use Filament\Forms\Components\DateTimePicker;

class CampaignDateTimePicker
{
    public static function make(string $name): DateTimePicker
    {
        return DateTimePicker::make($name)
                ->label('When should the journey step be executed?')
                ->columnSpanFull()
                ->timezone(app(CampaignSettings::class)->getActionExecutionTimezone())
                ->hintIconTooltip('This time is set in ' . app(CampaignSettings::class)->getActionExecutionTimezoneLabel() . '.')
                ->lazy()
                ->required()
                ->minDate(now());
    }
}