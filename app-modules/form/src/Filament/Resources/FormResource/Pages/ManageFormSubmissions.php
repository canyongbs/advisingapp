<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages;

use Illuminate\Database\Eloquent\Model;
use Assist\Form\Filament\Resources\FormResource;
use Filament\Resources\Pages\ManageRelatedRecords;
use Assist\Form\Filament\Resources\FormResource\RelationManagers\FormSubmissionsRelationManager;

class ManageFormSubmissions extends ManageRelatedRecords
{
    protected static string $resource = FormResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'submissions';

    protected static ?string $navigationLabel = 'Submissions';

    protected static ?string $breadcrumb = 'Submissions';

    public static function canAccess(?Model $record = null): bool
    {
        foreach ([
            FormSubmissionsRelationManager::class,
        ] as $relationManager) {
            if (! $relationManager::canViewForRecord($record, static::class)) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function getRelationManagers(): array
    {
        return [
            FormSubmissionsRelationManager::class,
        ];
    }
}
