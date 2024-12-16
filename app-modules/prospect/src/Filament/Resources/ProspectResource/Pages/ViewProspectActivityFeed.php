<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Resources\Pages\Page;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\Concerns\HasStudentHeader;
use AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Pages\Concerns\HasEducatableActivityFeed;

class ViewProspectActivityFeed extends Page
{
    use HasEducatableActivityFeed;
    use HasStudentHeader;

    protected static string $resource = ProspectResource::class;

    protected static ?string $title = 'Activity Feed';

    protected static string $view = 'prospect::filament.resources.prospect-resource.view-prospect-activity-feed';
}
