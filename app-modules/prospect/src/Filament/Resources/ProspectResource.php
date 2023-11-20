<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Prospect\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Assist\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Model;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\EditProspect;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ViewProspect;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\CreateProspect;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectFiles;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectTasks;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectAlerts;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectCareTeam;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectEngagement;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectInteractions;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ProspectEngagementTimeline;
use Assist\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectSubscriptions;

class ProspectResource extends Resource
{
    protected static ?string $model = Prospect::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Record Management';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'full_name';

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewProspect::class,
            EditProspect::class,
            ManageProspectEngagement::class,
            ManageProspectFiles::class,
            ManageProspectAlerts::class,
            ManageProspectTasks::class,
            ManageProspectSubscriptions::class,
            ManageProspectInteractions::class,
            ProspectEngagementTimeline::class,
            ManageProspectCareTeam::class,
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'email_2', 'mobile', 'phone'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Student ID' => $record->sisid,
            'Other ID' => $record->otherid,
            'Email Address' => collect([$record->email, $record->email_id])->filter()->implode(', '),
            'Phone' => collect([$record->mobile, $record->phone])->filter()->implode(', '),
        ], fn (mixed $value): bool => filled($value));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProspects::route('/'),
            'create' => CreateProspect::route('/create'),
            'edit' => EditProspect::route('/{record}/edit'),
            'manage-alerts' => ManageProspectAlerts::route('/{record}/alerts'),
            'manage-engagement' => ManageProspectEngagement::route('/{record}/engagement'),
            'manage-files' => ManageProspectFiles::route('/{record}/files'),
            'manage-interactions' => ManageProspectInteractions::route('/{record}/interactions'),
            'manage-subscriptions' => ManageProspectSubscriptions::route('/{record}/subscriptions'),
            'manage-tasks' => ManageProspectTasks::route('/{record}/tasks'),
            'view' => ViewProspect::route('/{record}'),
            'timeline' => ProspectEngagementTimeline::route('/{record}/timeline'),
            'care-team' => ManageProspectCareTeam::route('/{record}/care-team'),
        ];
    }
}
