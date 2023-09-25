<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\Pages;

use Assist\AssistDataModel\Models\Student;
use Assist\AssistDataModel\Filament\Resources\StudentResource;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\ManageInteractions;

class ManageStudentInteractions extends ManageInteractions
{
    protected static string $resource = StudentResource::class;

    protected static string $interactableType = Student::class;
}
