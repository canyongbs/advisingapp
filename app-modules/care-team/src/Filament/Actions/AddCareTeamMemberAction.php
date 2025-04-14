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

namespace AdvisingApp\CareTeam\Filament\Actions;

use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\CareTeamRoleType;
use App\Features\CareTeamRoleFeature;
use App\Models\Scopes\HasLicense;
use App\Models\User;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AddCareTeamMemberAction
{
    public static function make(CareTeamRoleType $context): BulkAction
    {
        return BulkAction::make('addCareTeamMember')
            ->label('Add Care Team Member')
            ->icon('heroicon-s-user-group')
            ->fillForm(fn (Collection $records): array => [
                'records' => $records,
                'care_team_role_id' => match ($context) {
                    CareTeamRoleType::Student => CareTeamRoleType::studentDefault()?->getKey(),
                    CareTeamRoleType::Prospect => CareTeamRoleType::prospectDefault()?->getKey(),
                },
            ])
            ->form([
                Select::make('recordId')
                    ->label('User')
                    ->searchable()
                    ->required()
                    ->options(User::query()->tap(new HasLicense(match ($context) {
                        CareTeamRoleType::Student => Student::getLicenseType(),
                        CareTeamRoleType::Prospect => Prospect::getLicenseType(),
                    }))->pluck('name', 'id')),
                Select::make('care_team_role_id')
                    ->label('Role')
                    ->relationship('careTeamRole', 'name', fn (Builder $query) => $query->where('type', match ($context) {
                        CareTeamRoleType::Student => CareTeamRoleType::Student,
                        CareTeamRoleType::Prospect => CareTeamRoleType::Prospect,
                    }))
                    ->searchable()
                    ->model(CareTeam::class)
                    ->visible(CareTeamRole::where('type', match ($context) {
                        CareTeamRoleType::Student => CareTeamRoleType::Student,
                        CareTeamRoleType::Prospect => CareTeamRoleType::Prospect,
                    })->count() > 0 && CareTeamRoleFeature::active()),
            ])
            ->action(function (Collection $records, array $data) {
                return $records
                    ->each(function (Model $record) use ($data) {
                        throw_unless($record instanceof Educatable, new Exception('Record must be of type educatable.'));

                        /** @var User $user */
                        $user = User::find($data['recordId']);

                        if ($record->careTeam()->where('user_id', $user->getKey())->doesntExist()) {
                            $record->careTeam()->attach($user, ['care_team_role_id' => $data['care_team_role_id']]);
                        }
                    });
            })
            ->deselectRecordsAfterCompletion();
    }
}
