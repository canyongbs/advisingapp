<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseManagement\Filament\Resources;

use AdvisingApp\CaseManagement\Enums\CaseEmailTemplateType;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\CreateCaseType;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\EditCaseType;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\EditCaseTypeAssignments;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\EditCaseTypeNotifications;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\ListCaseTypes;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\ManageCaseTypeAuditors;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\ManageCaseTypeEmailTemplate;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\ManageCaseTypeManagers;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\Pages\ViewCaseType;
use AdvisingApp\CaseManagement\Filament\Resources\CaseTypeResource\RelationManagers\CasePrioritiesRelationManager;
use AdvisingApp\CaseManagement\Models\CaseType;
use App\Filament\Clusters\CaseManagementAdministration;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class CaseTypeResource extends Resource
{
    protected static ?string $model = CaseType::class;

    protected static ?int $navigationSort = 10;

    protected static ?string $cluster = CaseManagementAdministration::class;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getRelations(): array
    {
        return [
            CasePrioritiesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCaseTypes::route('/'),
            'create' => CreateCaseType::route('/create'),
            'view' => ViewCaseType::route('/{record}'),
            'edit' => EditCaseType::route('/{record}/edit'),
            'case-type-managers' => ManageCaseTypeManagers::route('/{record}/managers'),
            'case-type-auditors' => ManageCaseTypeAuditors::route('/{record}/auditors'),
            'case-type-assignments' => EditCaseTypeAssignments::route('/{record}/assignments'),
            'case-type-notifications' => EditCaseTypeNotifications::route('/{record}/notifications'),
            'case-type-email-template' => ManageCaseTypeEmailTemplate::route('/{record}/email-template/{type}'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        assert(isset($page->record));

        return [
            ...$page->generateNavigationItems([
                ViewCaseType::class,
                EditCaseType::class,
                ManageCaseTypeManagers::class,
                ManageCaseTypeAuditors::class,
                EditCaseTypeAssignments::class,
                EditCaseTypeNotifications::class,
            ]),
            ...array_map(
                fn (CaseEmailTemplateType $type): NavigationItem => Arr::first(ManageCaseTypeEmailTemplate::getNavigationItems(['record' => $page->record, 'type' => $type]))
                    ->label($type->getLabel())
                    ->isActiveWhen(fn (): bool => Str::endsWith(request()->path(), $type->value)),
                CaseEmailTemplateType::cases(),
            ),
        ];
    }
}
