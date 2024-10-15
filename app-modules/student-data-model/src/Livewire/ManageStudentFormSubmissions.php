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

namespace AdvisingApp\StudentDataModel\Livewire;

use App\Enums\Feature;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ManageStudentEvents;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\RelationManagers\StudentFormSubmissionsRelationManager;

class ManageStudentFormSubmissions extends ManageRelatedRecords
{
    protected static string $resource = StudentResource::class;

    protected static string $relationship = 'formSubmissions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Form Submissions';

    protected static string $view = 'student-data-model::livewire.manage-student-form-submissions';

    public function mount(int | string $record): void
    {
        $this->record = $this->resolveRecord($record);

        $this->authorizeAccess();

        $this->previousUrl = url()->previous();

        $this->loadDefaultActiveTab();
    }

    public static function canAccess(array $parameters = []): bool
    {
        return parent::canAccess($parameters) && Gate::check(Feature::OnlineForms->getGateName());
    }

    // public static function getNavigationItems(array $urlParameters = []): array
    // {
    //     $item = parent::getNavigationItems($urlParameters)[0];

    //     $ownerRecord = $urlParameters['record'];

    //     /** @var Prospect $ownerRecord */
    //     $formSubmissionsCount = Cache::tags('form-submission-count')
    //         ->remember(
    //             "form-submission-count-{$ownerRecord->getKey()}",
    //             now()->addMinutes(5),
    //             function () use ($ownerRecord): int {
    //                 return $ownerRecord->formSubmissions()->count();
    //             },
    //         );

    //     $item->badge($formSubmissionsCount > 0 ? $formSubmissionsCount : null);

    //     return [$item];
    // }

    public function getRelationManagers(): array
    {
        return static::managers($this->getRecord());
    }

    private static function managers(?Model $record = null): array
    {
        return collect([
            StudentFormSubmissionsRelationManager::class,
            ManageStudentEvents::class,
        ])
            ->reject(fn ($relationManager) => $record && (! $relationManager::canViewForRecord($record, static::class)))
            ->toArray();
    }
}
