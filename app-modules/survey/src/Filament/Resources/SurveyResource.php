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

namespace AdvisingApp\Survey\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Resources\Pages\Page;
use AdvisingApp\Survey\Models\Survey;
use Illuminate\Database\Eloquent\Builder;
use AdvisingApp\Survey\Filament\Resources\SurveyResource\Pages\EditSurvey;
use AdvisingApp\Survey\Filament\Resources\SurveyResource\Pages\ListSurveys;
use AdvisingApp\Survey\Filament\Resources\SurveyResource\Pages\CreateSurvey;
use AdvisingApp\Survey\Filament\Resources\SurveyResource\Pages\ManageSurveySubmissions;

class SurveyResource extends Resource
{
    protected static ?string $model = Survey::class;

    protected static ?string $navigationGroup = 'Premium Features';

    protected static ?int $navigationSort = 70;

    protected static ?string $navigationLabel = 'Online Surveys';

    protected static ?string $breadcrumb = 'Online Surveys';

    protected static ?string $modelLabel = 'Survey';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['fields']);
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            EditSurvey::class,
            ManageSurveySubmissions::class,
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSurveys::route('/'),
            'create' => CreateSurvey::route('/create'),
            'edit' => EditSurvey::route('/{record}/edit'),
            'manage-submissions' => ManageSurveySubmissions::route('/{record}/submissions'),
        ];
    }
}
