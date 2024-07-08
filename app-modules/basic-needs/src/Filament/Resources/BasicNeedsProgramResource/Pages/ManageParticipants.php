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
