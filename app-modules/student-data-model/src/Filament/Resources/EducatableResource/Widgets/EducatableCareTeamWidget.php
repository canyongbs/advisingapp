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

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Widgets;

use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use App\Models\User;
use Exception;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;

class EducatableCareTeamWidget extends Widget
{
    protected static string $view = 'student-data-model::filament.resources.educatable-resource.widgets.educatable-care-team-widget';

    #[Locked]
    public Educatable&Model $educatable;

    #[Locked]
    public string $manageUrl;

    public static function canView(): bool
    {
        return auth()->user()->can('viewAny', CareTeam::class);
    }

    protected function getCareTeam(): array
    {
        return $this->educatable->careTeam()
            ->orderBy('care_teams.created_at')
            ->get()
            ->map(function (Model $user) {
                throw_unless($user instanceof User, new Exception('Care team models must be users.'));

                $user->careTeamRole = $user->getCareTeamRoleFor($this->educatable->getKey());

                return $user;
            })
            ->all();
    }
}
