<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use Laravel\Pennant\Feature;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers\ProgramRelationManager;

class ManageProspectPrograms extends ManageRelatedRecords
{
    protected static string $resource = ProspectResource::class;

    protected static string $relationship = 'basicNeedsPrograms';

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $breadcrumb = 'Programs';

    protected static ?string $title = 'Programs';

    public static function getNavigationLabel(): string
    {
        return 'Programs';
    }

    public function getRelationManagers(): array
    {
        return [ProgramRelationManager::class];
    }

    public static function canAccess(array $parameters = []): bool
    {
        if (Feature::active('manage-program-participants')) {
            return parent::canAccess($parameters);
        }

        return false;
    }
}
