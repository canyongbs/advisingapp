<?php

namespace AdvisingApp\Prospect\Filament\Resources;

use Filament\Pages\Page;
use Filament\Resources\Resource;
use AdvisingApp\Prospect\Models\Pipeline;
use AdvisingApp\Prospect\Settings\ProspectPipelineSettings;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\EditPipeline;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\ViewPipeline;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\ListPipelines;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\CreatePipeline;
use AdvisingApp\Prospect\Filament\Resources\PipelineResource\Pages\ManageEductables;
use AdvisingApp\Prospect\Models\Prospect;
use App\Features\PipelineFlag;

class PipelineResource extends Resource
{
    protected static ?string $model = Pipeline::class;

    protected static ?string $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationGroup = 'Recruitment CRM';

    protected static ?int $navigationSort = 30;

    public static function canAccess(): bool
    {
        return parent::canAccess() && PipelineFlag::active() && app(ProspectPipelineSettings::class)->is_enabled;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewPipeline::class,
            EditPipeline::class,
            ManageEductables::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPipelines::route('/'),
            'create' => CreatePipeline::route('/create'),
            'edit' => EditPipeline::route('/{record}/edit'),
            'view' => ViewPipeline::route('/{record}'),
            'manage' => ManageEductables::route('/manage/{record}'),
        ];
    }
}
