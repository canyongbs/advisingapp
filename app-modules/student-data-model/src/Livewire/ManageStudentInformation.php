<?php

namespace AdvisingApp\StudentDataModel\Livewire;

use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentFiles;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentInteractions;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\ProgramsRelationManager;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\EnrollmentsRelationManager;

class ManageStudentInformation extends ManageRelatedRecords
{
    protected static string $view = 'student-data-model::livewire.manage-student-information';

    protected static string $resource = StudentResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'programs';

    protected static ?string $navigationLabel = 'Information';

    protected static ?string $breadcrumb = 'Information';

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->previousUrl = url()->previous();

        $this->loadDefaultActiveTab();
    }

    public static function canAccess(array $arguments = []): bool
    {
        return (bool) count(static::managers($arguments['record'] ?? null));
    }

    public function getRelationManagers(): array
    {
        return static::managers($this->getRecord());
    }

    private static function managers(?Model $record = null): array
    {
        return collect([
            ProgramsRelationManager::class,
            EnrollmentsRelationManager::class,
            ManageStudentInteractions::class,
            ManageStudentFiles::class,
        ])
            ->reject(fn ($relationManager) => $record && (! $relationManager::canViewForRecord($record, static::class)))
            ->toArray();
    }
}
