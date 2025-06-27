<?php

namespace AdvisingApp\Workflow\Filament\Resources\WorkflowResource\Pages;

use AdvisingApp\Workflow\Filament\Resources\WorkflowResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewWorkflow extends ViewRecord
{
    protected static string $resource = WorkflowResource::class;

    protected static ?string $navigationLabel = 'Workflow';

    protected static ?string $breadcrumb = 'Workflow';

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
