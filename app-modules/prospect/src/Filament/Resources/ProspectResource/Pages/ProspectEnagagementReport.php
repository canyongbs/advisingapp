<?php

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use Filament\Resources\Pages\Page;
use App\Filament\Clusters\Prospect;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;

class ProspectEnagagementReport extends Page
{
    protected static string $resource = ProspectResource::class;

    protected static ?string $cluster = Prospect::class;

    protected static string $view = 'advising-prospect.filament.resources.prospect-resource.pages.prospect-enagagement-report';
}
