<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Prospect\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\Prospect\Models\Prospect;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\Concerns\HasGlobalSearchResultScoring;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\EditProspect;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ViewProspect;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ListProspects;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\CreateProspect;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectFiles;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectTasks;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectAlerts;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectEvents;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectCareTeam;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectPrograms;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ProspectCaseManagement;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectEngagement;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectInteractions;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ProspectEngagementTimeline;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectSubscriptions;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectFormSubmissions;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ManageProspectApplicationSubmissions;

class ProspectResource extends Resource
{
    use HasGlobalSearchResultScoring;

    protected static ?string $model = Prospect::class;

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationGroup = 'Recruitment CRM';

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
            ManageProspectFormSubmissions::class,
            ManageProspectApplicationSubmissions::class,
            ProspectCaseManagement::class,
            ManageProspectEvents::class,
            ManageProspectPrograms::class,
        ]);
    }

    public static function modifyGlobalSearchQuery(Builder $query, string $search): void
    {
        static::scoreGlobalSearchResults($query, $search, [
            'full_name' => 100,
            'email' => 75,
            'email_2' => 75,
        ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['full_name', 'email', 'email_2', 'mobile', 'phone', 'preferred'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return array_filter([
            'Student ID' => $record->sisid,
            'Other ID' => $record->otherid,
            'Email Address' => collect([$record->email, $record->email_id])->filter()->implode(', '),
            'Phone' => collect([$record->mobile, $record->phone])->filter()->implode(', '),
            'Preferred Name' => $record->preferred,
        ], fn (mixed $value): bool => filled($value));
    }

    public static function getGlobalSearchResultUrl(Model $record): string
    {
        return ProspectResource::getUrl('view', ['record' => $record]);
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
            'manage-form-submissions' => ManageProspectFormSubmissions::route('/{record}/form-submissions'),
            'manage-application-submissions' => ManageProspectApplicationSubmissions::route('/{record}/application-submissions'),
            'manage-interactions' => ManageProspectInteractions::route('/{record}/interactions'),
            'manage-subscriptions' => ManageProspectSubscriptions::route('/{record}/subscriptions'),
            'manage-tasks' => ManageProspectTasks::route('/{record}/tasks'),
            'view' => ViewProspect::route('/{record}'),
            'timeline' => ProspectEngagementTimeline::route('/{record}/timeline'),
            'care-team' => ManageProspectCareTeam::route('/{record}/care-team'),
            'case-management' => ProspectCaseManagement::route('/{record}/case-management'),
            'events' => ManageProspectEvents::route('/{record}/events'),
            'programs' => ManageProspectPrograms::route('/{record}/programs'),
        ];
    }
}
