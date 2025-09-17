<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\AdvisingApp\StudentDataModel\Models\EnrollmentSemesterResource\Pages;

use AdvisingApp\StudentDataModel\Filament\Resources\AdvisingApp\StudentDataModel\Models\EnrollmentSemesterResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEnrollmentSemesters extends ManageRecords
{
    protected static string $resource = EnrollmentSemesterResource::class;

    protected static ?string $title = 'Semester Order';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
