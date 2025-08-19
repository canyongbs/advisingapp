<?php

namespace AdvisingApp\Ai\Filament\Resources\DataAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\DataAdvisorResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDataAdvisors extends ListRecords
{
    protected static string $resource = DataAdvisorResource::class;

    protected static string $view = 'filament.pages.coming-soon';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
