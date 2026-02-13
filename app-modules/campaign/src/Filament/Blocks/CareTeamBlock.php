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

namespace AdvisingApp\Campaign\Filament\Blocks;

use AdvisingApp\Campaign\Filament\Forms\Components\CampaignDateTimePicker;
use AdvisingApp\Campaign\Filament\Resources\Campaigns\Pages\CreateCampaign;
use AdvisingApp\CareTeam\Models\CareTeam;
use AdvisingApp\CareTeam\Models\CareTeamRole;
use AdvisingApp\Group\Models\Group;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Enums\CareTeamRoleType;
use App\Models\Scopes\HasLicense;
use App\Models\User;
use Carbon\CarbonImmutable;
use Exception;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder;

class CareTeamBlock extends CampaignActionBlock
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->label('Care Team');

        $this->schema($this->createFields());
    }

    public function generateFields(string $fieldPrefix = ''): array
    {
        return [
            Repeater::make($fieldPrefix . 'careTeam')
                ->label('Who should be assigned to the care team?')
                ->schema([
                    Select::make('user_id')
                        ->label('User')
                        ->options(function (Get $get, $livewire, string $operation) {
                            if ($livewire instanceof CreateCampaign) {
                                $groupId = $get('../../../../../segment_id');
                            } else {
                                $groupId = $livewire->getOwnerRecord()->segment_id;
                            }
                            $group = Group::find($groupId);

                            return User::query()->tap(new HasLicense(match ($group->model->getLabel()) {
                                CareTeamRoleType::Student->getLabel() => Student::getLicenseType(),
                                CareTeamRoleType::Prospect->getLabel() => Prospect::getLicenseType(),
                                default => null,
                            }))->pluck('name', 'id');
                        })
                        ->searchable()
                        ->required()
                        ->exists('users', 'id'),
                    Select::make('care_team_role_id')
                        ->label('Role')
                        ->relationship('careTeamRole', 'name', function (Builder $query, Get $get, $livewire, string $operation) {
                            if ($livewire instanceof CreateCampaign) {
                                $groupId = $get('../../../../../segment_id');
                            } else {
                                $groupId = $livewire->getOwnerRecord()->segment_id;
                            }
                            $group = Group::find($groupId);

                            $query->where('type', match ($group->model->getLabel()) {
                                CareTeamRoleType::Student->getLabel() => CareTeamRoleType::Student,
                                CareTeamRoleType::Prospect->getLabel() => CareTeamRoleType::Prospect,
                                default => throw new Exception('The group population was not of a type that can have a care team role associated with it.'),
                            })->orderByDesc('created_at');
                        })
                        ->preload()
                        ->optionsLimit(20)
                        ->searchable()
                        ->default(function (Get $get, $livewire, string $operation) {
                            if ($livewire instanceof CreateCampaign) {
                                $groupId = $get('../../../../../segment_id');
                            } else {
                                $groupId = $livewire->getOwnerRecord()->segment_id;
                            }
                            $group = Group::find($groupId);

                            return match ($group->model->getLabel()) {
                                CareTeamRoleType::Student->getLabel() => CareTeamRoleType::studentDefault()?->getKey(),
                                CareTeamRoleType::Prospect->getLabel() => CareTeamRoleType::prospectDefault()?->getKey(),
                                default => throw new Exception('The group population was not of a type that can have a care team role associated with it.'),
                            };
                        })
                        ->model(CareTeam::class)
                        ->visible(function (Get $get, $livewire, string $operation) {
                            if ($livewire instanceof CreateCampaign) {
                                $groupId = $get('../../../../../segment_id');
                            } else {
                                $groupId = $livewire->getOwnerRecord()->segment_id;
                            }
                            $group = Group::find($groupId);

                            return CareTeamRole::where('type', match ($group->model->getLabel()) {
                                CareTeamRoleType::Student->getLabel() => CareTeamRoleType::Student,
                                CareTeamRoleType::Prospect->getLabel() => CareTeamRoleType::Prospect,
                                default => throw new Exception('The group population was not of a type that can have a care team role associated with it.'),
                            })->count() > 0;
                        }),
                ])
                ->addActionLabel('Add User')
                ->reorderable(false),

            Toggle::make($fieldPrefix . 'remove_prior')
                ->label('Remove all prior care team assignments?')
                ->default(false)
                ->hintIconTooltip('If checked, all prior care team assignments will be removed.'),
            CampaignDateTimePicker::make('execute_at')
                ->helperText(fn ($state): ?string => filled($state) ? $this->generateUserTimezoneHint(CarbonImmutable::parse($state)) : null),
        ];
    }

    public static function type(): string
    {
        return 'care_team';
    }
}
