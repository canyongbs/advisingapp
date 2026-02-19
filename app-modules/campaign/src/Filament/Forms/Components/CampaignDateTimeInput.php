<?php

namespace AdvisingApp\Campaign\Filament\Forms\Components;

use AdvisingApp\Campaign\Filament\Blocks\CampaignActionBlock;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\CreateCampaign;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\RelationManagers\CampaignActionsRelationManager;
use AdvisingApp\Campaign\Settings\CampaignSettings;
use Carbon\CarbonImmutable;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;

class CampaignDateTimeInput
{
    public static function make(): Group
    {
        return Group::make()
            ->schema([
                ToggleButtons::make('input_type')
                    ->label('How would you like to select when this step occurs?')
                    ->options([
                        'fixed' => 'Fixed Date',
                        'relative' => 'Relative Date',
                    ])
                    ->inline()
                    ->live()
                    ->visible(
                        fn (Get $get, Component $component, Page|CampaignActionsRelationManager $livewire) => 
                            ! self::isFirstBlock($get, $component) &&
                            $livewire instanceof CreateCampaign
                    )
                    ->required(),
                DateTimePicker::make('execute_at')
                    ->label('When should the journey step be executed?')
                    ->visible(
                        fn (Get $get, Component $component, Page|CampaignActionsRelationManager $livewire) => 
                            ! ($livewire instanceof CreateCampaign) ||
                            self::isFirstBlock($get, $component) ||
                            $get('input_type') === 'fixed'
                    )
                    ->columnSpanFull()
                    ->timezone(app(CampaignSettings::class)->getActionExecutionTimezone())
                    ->hintIconTooltip('This time is set in ' . app(CampaignSettings::class)->getActionExecutionTimezoneLabel() . '.')
                    ->lazy()
                    ->helperText(fn ($state): ?string => filled($state) ? CampaignActionBlock::generateUserTimezoneHint(CarbonImmutable::parse($state)) : null)
                    ->required()
                    ->minDate(now()),
                Section::make('How long after the previous step should this occur?')
                    ->schema([
                        TextInput::make('days')
                            ->translateLabel()
                            ->numeric()
                            ->step(1)
                            ->minValue(0)
                            ->default(0),
                        TextInput::make('hours')
                            ->translateLabel()
                            ->numeric()
                            ->step(1)
                            ->minValue(0)
                            ->default(0),
                        TextInput::make('minutes')
                            ->translateLabel()
                            ->numeric()
                            ->step(1)
                            ->minValue(0)
                            ->default(0),
                    ])
                    ->visible(
                        fn (Get $get, Component $component, Page|CampaignActionsRelationManager $livewire) =>
                            ! self::isFirstBlock($get, $component) &&
                            $get('input_type') === 'relative' &&
                            $livewire instanceof CreateCampaign
                    )
                    ->columns(3),
            ]);
    }

    private static function isFirstBlock(Get $get, Component $component): bool
    {
        // Compare the current block's id against the first block's id
        return array_key_first($get('../../')) === explode('.', $component->getStatePath())[2];
    }
}
