<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseManagement\Filament\Resources\Cases;

use AdvisingApp\CaseManagement\Filament\Resources\Cases\Pages\CaseTimeline;
use AdvisingApp\CaseManagement\Filament\Resources\Cases\Pages\CreateCase;
use AdvisingApp\CaseManagement\Filament\Resources\Cases\Pages\EditCase;
use AdvisingApp\CaseManagement\Filament\Resources\Cases\Pages\ListCases;
use AdvisingApp\CaseManagement\Filament\Resources\Cases\Pages\ManageCaseAssignment;
use AdvisingApp\CaseManagement\Filament\Resources\Cases\Pages\ManageCaseFormSubmission;
use AdvisingApp\CaseManagement\Filament\Resources\Cases\Pages\ManageCaseInteraction;
use AdvisingApp\CaseManagement\Filament\Resources\Cases\Pages\ManageCaseUpdate;
use AdvisingApp\CaseManagement\Filament\Resources\Cases\Pages\ViewCase;
use AdvisingApp\CaseManagement\Models\CaseModel;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;

class CaseResource extends Resource
{
    protected static ?string $model = CaseModel::class;

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Case';

    // TODO: Look into whether or not we should just delete this resource
    protected static bool $shouldRegisterNavigation = false;

    public static function shouldShowFormSubmission(Page $page): bool
    {
        if (! is_null($page->record) && ! is_null($page->record->caseFormSubmission)) {
            return true;
        }

        return false;
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        $navigationItems = [
            ViewCase::class,
            EditCase::class,
            ManageCaseAssignment::class,
            ManageCaseUpdate::class,
            ManageCaseInteraction::class,
            CaseTimeline::class,
        ];

        if (static::shouldShowFormSubmission($page)) {
            array_splice($navigationItems, 1, 0, ManageCaseFormSubmission::class);
        }

        return $page->generateNavigationItems($navigationItems);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCases::route('/'),
            'manage-assignments' => ManageCaseAssignment::route('/{record}/users'),
            'manage-case-updates' => ManageCaseUpdate::route('/{record}/updates'),
            'manage-interactions' => ManageCaseInteraction::route('/{record}/interactions'),
            'manage-case-form-submission' => ManageCaseFormSubmission::route('/{record}/form-submission'),
            'create' => CreateCase::route('/create'),
            'view' => ViewCase::route('/{record}'),
            'edit' => EditCase::route('/{record}/edit'),
            'timeline' => CaseTimeline::route('/{record}/timeline'),
        ];
    }
}
