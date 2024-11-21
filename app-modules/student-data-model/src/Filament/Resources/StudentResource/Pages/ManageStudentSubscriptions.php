<?php

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\Concerns\HasStudentHeader;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Pages\Concerns\CanManageEducatableSubscriptions;

class ManageStudentSubscriptions extends ManageRelatedRecords
{
    use CanManageEducatableSubscriptions;
    use HasStudentHeader;

    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'subscribedUsers';

    protected static ?string $title = 'Subscriptions';
}
