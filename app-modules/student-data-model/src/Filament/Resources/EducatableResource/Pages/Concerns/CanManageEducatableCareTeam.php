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

namespace AdvisingApp\StudentDataModel\Filament\Resources\EducatableResource\Pages\Concerns;

use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\CareTeamRoleType;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\HasLicense;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

trait CanManageEducatableCareTeam
{
    public static function canAccess(array $parameters = []): bool
    {
        if (! static::getResource()::canView($parameters['record'])) {
            return false;
        }

        return auth()->user()->can('viewAny', CareTeam::class);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->url(fn ($record) => UserResource::getUrl('view', ['record' => $record]))
                    ->color('primary'),
                TextColumn::make('job_title'),
                TextColumn::make('careTeams.studentCareTeamRole.name')
                    ->state(fn ($record) => CareTeamRole::find($record->care_team_role_id)?->name)
                    ->label('Role')
                    ->badge()
                    ->visible(CareTeamRole::where('type', CareTeamRoleType::Student)->count() > 0),
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('New')
                    ->modalHeading(function () {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return "Add a User to {$student->display_name}'s Care Team";
                    })
                    ->modalSubmitActionLabel('Add')
                    ->attachAnother(false)
                    ->color('primary')
                    ->mountUsing(fn (Schema $schema) => $schema->fill([
                        'care_team_role_id' => CareTeamRoleType::studentDefault()?->id,
                    ]))
                    ->form([
                        Select::make('recordId')
                            ->label('User')
                            ->searchable()
                            ->required()
                            ->options(
                                User::query()->tap(new HasLicense(Student::getLicenseType()))
                                    ->whereDoesntHave(
                                        'studentCareTeams',
                                        fn ($query) => $query
                                            ->where('educatable_type', $this->getOwnerRecord()->getMorphClass())
                                            ->where('educatable_id', $this->getOwnerRecord()->getKey())
                                    )->pluck('name', 'id')
                            ),
                        Select::make('care_team_role_id')
                            ->label('Role')
                            ->relationship('careTeamRole', 'name', fn (Builder $query) => $query->where('type', CareTeamRoleType::Student)->orderByDesc('created_at'))
                            ->preload()
                            ->optionsLimit(20)
                            ->searchable()
                            ->model(CareTeam::class)
                            ->visible(CareTeamRole::where('type', CareTeamRoleType::Student)->count() > 0),
                    ])
                    ->successNotificationTitle(function (array $data) {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        $record = User::find($data['recordId']);

                        return "{$record->name} was added to {$student->display_name}'s Care Team";
                    }),
            ])
            ->recordActions([
                DetachAction::make()
                    ->label('Remove')
                    ->modalHeading(function (User $record) {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return "Remove {$record->name} from {$student->display_name}'s Care Team";
                    })
                    ->modalSubmitActionLabel('Remove')
                    ->successNotificationTitle(function (User $record) {
                        /** @var Student $student */
                        $student = $this->getOwnerRecord();

                        return "{$record->name} was removed from {$student->display_name}'s Care Team";
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->label('Remove selected')
                        ->modalHeading(function () {
                            /** @var Student $student */
                            $student = $this->getOwnerRecord();

                            return "Remove selected users from {$student->display_name}'s Care Team";
                        })
                        ->modalSubmitActionLabel('Remove')
                        ->successNotificationTitle(function () {
                            /** @var Student $student */
                            $student = $this->getOwnerRecord();

                            return "All selected users were removed from {$student->display_name}'s Care Team";
                        }),
                ]),
            ])
            ->emptyStateHeading('No Users')
            ->inverseRelationship('studentCareTeams');
    }
}
