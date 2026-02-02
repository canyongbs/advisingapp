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

namespace AdvisingApp\CareTeam\Filament\Actions;

use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\CareTeamRoleType;
use App\Models\Scopes\HasLicense;
use App\Models\Scopes\WithoutAnyAdmin;
use App\Models\User;
use Filament\Actions\AttachAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ViewField;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Enums\Width;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class AddCareTeamMemberToEducatableAttachAction extends AttachAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('New')
            ->modalHeading(fn () => "Add Users to {$this->getLivewire()->getOwnerRecord()->display_name}'s Care Team")
            ->modalSubmitActionLabel('Add')
            ->modalWidth(Width::FourExtraLarge)
            ->attachAnother(false)
            ->color('primary')
            ->steps(function () {
                $careTeamRoleExists = CareTeamRole::where(
                    'type',
                    match (true) {
                        $this->getLivewire()->getOwnerRecord() instanceof Student => CareTeamRoleType::Student,
                        $this->getLivewire()->getOwnerRecord() instanceof Prospect => CareTeamRoleType::Prospect,
                        default => null,
                    }
                )
                    ->count() > 0;

                return [
                    Step::make('Add Care Team Members')
                        ->schema([
                            Repeater::make('careTeams')
                                ->label(fn () => "Please add one or more staff members to the care team for the {$this->getLivewire()->getOwnerRecord()->getMorphClass()} {$this->getLivewire()->getOwnerRecord()->display_name}")
                                ->table(function () use ($careTeamRoleExists) {
                                    $columns = [
                                        TableColumn::make('User')
                                            ->markAsRequired(),
                                    ];

                                    if ($careTeamRoleExists) {
                                        $columns[] = TableColumn::make('Care Team Role');
                                    }

                                    return $columns;
                                })
                                ->compact()
                                ->schema(function () use ($careTeamRoleExists) {
                                    return [
                                        Select::make('user_id')
                                            ->searchable()
                                            ->required()
                                            ->disableOptionsWhenSelectedInSiblingRepeaterItems() 
                                            ->options(match (true) {
                                                $this->getLivewire()->getOwnerRecord() instanceof Student => User::query()->tap(new HasLicense(Student::getLicenseType()))
                                                    ->whereDoesntHave(
                                                        'studentCareTeams',
                                                        fn ($query) => $query
                                                            ->where('educatable_type', $this->getLivewire()->getOwnerRecord()->getMorphClass())
                                                            ->where('educatable_id', $this->getLivewire()->getOwnerRecord()->getKey())
                                                    )
                                                    ->tap(new WithoutAnyAdmin())
                                                    ->pluck('name', 'id'),
                                                $this->getLivewire()->getOwnerRecord() instanceof Prospect => User::query()->tap(new HasLicense(Prospect::getLicenseType()))
                                                    ->whereDoesntHave(
                                                        'prospectCareTeams',
                                                        fn ($query) => $query
                                                            ->where('educatable_type', $this->getLivewire()->getOwnerRecord()->getMorphClass())
                                                            ->where('educatable_id', $this->getLivewire()->getOwnerRecord()->getKey())
                                                    )
                                                    ->tap(new WithoutAnyAdmin())
                                                    ->pluck('name', 'id'),
                                                default => null,
                                            }),
                                        Select::make('care_team_role_id')
                                            ->relationship(
                                                'careTeamRole',
                                                'name',
                                                fn (Builder $query) => $query
                                                    ->where(
                                                        'type',
                                                        match (true) {
                                                            $this->getLivewire()->getOwnerRecord() instanceof Student => CareTeamRoleType::Student,
                                                            $this->getLivewire()->getOwnerRecord() instanceof Prospect => CareTeamRoleType::Prospect,
                                                            default => null,
                                                        }
                                                    )
                                                    ->orderByDesc('created_at')
                                            )
                                            ->default(fn () => match (true) {
                                                $this->getLivewire()->getOwnerRecord() instanceof Student => CareTeamRoleType::studentDefault()?->id,
                                                $this->getLivewire()->getOwnerRecord() instanceof Prospect => CareTeamRoleType::prospectDefault()?->id,
                                                default => null,
                                            })
                                            ->preload()
                                            ->optionsLimit(20)
                                            ->searchable()
                                            ->model(CareTeam::class)
                                            ->visible($careTeamRoleExists),
                                    ];
                                })
                                ->addActionLabel('Add User')
                                ->reorderable(false),
                        ]),
                    Step::make('Confirm New Care Team Members')
                        ->schema([
                            ViewField::make('summary')
                                ->view(
                                    'filament.forms.components.care-teams.summary',
                                    fn (Get $get) => ['careTeams' => $get('careTeams'), 'educatable' => $this->getLivewire()->getOwnerRecord()]
                                ),
                        ]),
                ];
            })
            ->action(function (array $data) {
                try {
                    DB::beginTransaction();

                    foreach ($data['careTeams'] as $careTeam) {
                        $user = User::findOrFail($careTeam['user_id']);

                        $educatable = $this->getLivewire()->getOwnerRecord();

                        assert($educatable instanceof Student || $educatable instanceof Prospect);

                        if ($educatable->careTeam()->where('user_id', $user->getKey())->doesntExist()) {
                            $educatable->careTeam()->attach($user, ['care_team_role_id' => $careTeam['care_team_role_id'] ?? null]);
                        }
                    }

                    DB::commit();
                } catch (Throwable $throw) {
                    DB::rollBack();

                    throw $throw;
                }
            })
            ->modalSubmitActionLabel('Confirm')
            ->successNotificationTitle(
                fn (array $data) => count($data['careTeams']) . ' ' . Str::plural('User', count($data['careTeams'])) . " added to {$this->getLivewire()->getOwnerRecord()->display_name}'s Care Team"
            );
    }

    public static function getDefaultName(): ?string
    {
        return 'attach';
    }
}
