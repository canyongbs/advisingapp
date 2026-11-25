<?php

namespace AdvisingApp\Organization\Filament\Resources\Organizations\Pages;

use AdvisingApp\Organization\Filament\Resources\Organizations\OrganizationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOrganizations extends ListRecords
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modal()
                ->slideOver(),
        ];
    }
}
