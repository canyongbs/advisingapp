<?php

namespace Assist\Prospect\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Assist\Prospect\Models\Prospect;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ProspectEngagementTimeline;

class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Record Management';

    protected static ?int $navigationSort = 2;

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewProspect::class,
            Pages\EditProspect::class,
            Pages\ManageProspectEngagement::class,
            Pages\ManageProspectFiles::class,
            Pages\ManageProspectAlerts::class,
            Pages\ManageProspectTasks::class,
            Pages\ManageProspectSubscriptions::class,
            Pages\ManageProspectInteractions::class,
            ProspectEngagementTimeline::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProspects::route('/'),
            'create' => Pages\CreateProspect::route('/create'),
            'edit' => Pages\EditProspect::route('/{record}/edit'),
            'manage-alerts' => Pages\ManageProspectAlerts::route('/{record}/alerts'),
            'manage-engagement' => Pages\ManageProspectEngagement::route('/{record}/engagement'),
            'manage-files' => Pages\ManageProspectFiles::route('/{record}/files'),
            'manage-interactions' => Pages\ManageProspectInteractions::route('/{record}/interactions'),
            'manage-subscriptions' => Pages\ManageProspectSubscriptions::route('/{record}/subscriptions'),
            'manage-tasks' => Pages\ManageProspectTasks::route('/{record}/tasks'),
            'view' => Pages\ViewProspect::route('/{record}'),
            'engagement-timeline' => ProspectEngagementTimeline::route('/{record}/engagement-timeline'),
        ];
    }
}
