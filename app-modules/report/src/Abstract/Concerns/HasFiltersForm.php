<?php

namespace AdvisingApp\Report\Abstract\Concerns;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm as ConcernsHasFiltersForm;

trait HasFiltersForm
{
    use ConcernsHasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now())
                            ->afterStateUpdated(function ($set, $state, Get $get) {
                                if (blank($get('endDate')) && filled($state)) {
                                    $set('endDate', $state);
                                }
                            }),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now())
                            ->afterStateUpdated(function ($set, $state, Get $get) {
                                if (blank($get('startDate')) && filled($state)) {
                                    $set('startDate', $state);
                                }
                            }),
                    ])
                    ->columns(2),
            ]);
    }
}
