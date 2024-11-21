<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\Concerns\HasStudentHeader;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Pages\Concerns\CanManageEducatableCareTeam;

class ManageStudentCareTeam extends ManageRelatedRecords
{
    use CanManageEducatableCareTeam;
    use HasStudentHeader;

    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'careTeam';

    protected static ?string $title = 'Care Team';
}
