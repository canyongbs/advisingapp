<?php

namespace AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\Pages;

use Laravel\Pennant\Feature;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers\StudentsRelationManager;
use AdvisingApp\BasicNeeds\Filament\Resources\BasicNeedsProgramResource\RelationManagers\ProspectsRelationManager;

class ManageParticipants extends ManageRelatedRecords
{
    protected static string $resource = BasicNeedsProgramResource::class;

    protected static string $relationship = 'students';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    public static function getNavigationLabel(): string
    {
        return 'Participants';
    }

    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();

        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $this->getOwnerRecord()]) => $this->getOwnerRecord()->name,
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public static function canAccess(array $parameters = []): bool
    {
        if (Feature::active('manage-program-participants')) {
            return true;
        }

        return false;
    }

    public function getRelationManagers(): array
    {
        return [
            StudentsRelationManager::class,
            ProspectsRelationManager::class,
        ];
    }
}
