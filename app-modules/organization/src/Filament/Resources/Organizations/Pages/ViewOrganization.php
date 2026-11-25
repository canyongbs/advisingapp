<?php

namespace AdvisingApp\Organization\Filament\Resources\Organizations\Pages;

use AdvisingApp\Organization\Filament\Resources\Organizations\OrganizationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrganization extends ViewRecord
{
    protected static string $resource = OrganizationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->modal()
                ->slideOver(),
            DeleteAction::make(),
        ];
    }
}
