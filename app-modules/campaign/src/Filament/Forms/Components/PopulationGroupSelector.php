<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Campaign\Filament\Forms\Components;

use AdvisingApp\Campaign\Enums\GroupOwnership;
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Group\Enums\GroupModel;
use AdvisingApp\Group\Models\Group;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class PopulationGroupSelector
{
    /**
     * @return array<int, Select | ToggleButtons>
     */
    public static function make(?Closure $afterSegmentIdUpdated = null): array
    {
        $availablePopulationTypes = self::availablePopulationTypes();

        $segmentIdField = Select::make('segment_id')
            ->label('Population Group')
            ->options(function (Get $get, ?Campaign $record) {
                $populationType = self::authoritativePopulationType($record, $get);
                $groupOwnership = $get->enum('group_ownership', GroupOwnership::class, isNullable: true);

                if (blank($populationType) || blank($groupOwnership)) {
                    return [];
                }

                // Group Ownership is client-side state, so "All" must be re-authorized here rather than trusted from the form.
                if (($groupOwnership === GroupOwnership::All) && auth()->user()->cannot('group.view-any') && auth()->user()->cannot('group.*.view')) {
                    return [];
                }

                $currentUserDepartmentId = auth()->user()?->team_id;

                $query = Group::query()->where('model', $populationType);

                match ($groupOwnership) {
                    GroupOwnership::Mine => $query->where('user_id', auth()->id()),
                    GroupOwnership::Department => $query->whereHas('user', function (Builder $query) use ($currentUserDepartmentId) {
                        $query->when(
                            filled($currentUserDepartmentId),
                            fn (Builder $query) => $query->where('team_id', $currentUserDepartmentId),
                            fn (Builder $query) => $query->whereRaw('1 = 0'),
                        );
                    }),
                    GroupOwnership::All => null,
                };

                return $query->orderBy('name')->pluck('name', 'id');
            })
            ->rule(function (Get $get, ?Campaign $record): Closure {
                return function (string $attribute, mixed $value, Closure $fail) use ($get, $record) {
                    $populationType = self::authoritativePopulationType($record, $get);

                    if (blank($populationType) || blank($value)) {
                        return;
                    }

                    // Population Type and the selected group are re-validated here in case either was tampered with via Livewire state.
                    if (! Group::query()->where('model', $populationType)->whereKey($value)->exists()) {
                        $fail('The selected population group does not match the campaign\'s population type.');
                    }
                };
            })
            ->searchable()
            ->required()
            ->live();

        if ($afterSegmentIdUpdated) {
            $segmentIdField->afterStateUpdated($afterSegmentIdUpdated);
        }

        return [
            ToggleButtons::make('population_type')
                ->label('Population Type')
                ->options(collect($availablePopulationTypes)->mapWithKeys(fn (GroupModel $type) => [
                    $type->value => Str::ucfirst($type->getPluralLabel()),
                ]))
                ->inline()
                ->live()
                ->dehydrated(false)
                ->required()
                ->hidden(count($availablePopulationTypes) <= 1)
                ->disabled(fn (?Campaign $record) => $record !== null)
                ->helperText(fn (?Campaign $record) => $record !== null ? 'The population type cannot be changed once a campaign has been created.' : null)
                ->afterStateHydrated(fn (Set $set, ?Campaign $record) => $set(
                    'population_type',
                    self::defaultPopulationType($record, $availablePopulationTypes),
                ))
                ->afterStateUpdated(fn (Set $set) => $set('segment_id', null)),
            ToggleButtons::make('group_ownership')
                ->label('Group Ownership')
                ->options(function () {
                    $options = [
                        GroupOwnership::Mine->value => GroupOwnership::Mine->getLabel(),
                        GroupOwnership::Department->value => GroupOwnership::Department->getLabel(),
                    ];

                    if (auth()->user()->canAny(['group.view-any', 'group.*.view'])) {
                        $options[GroupOwnership::All->value] = GroupOwnership::All->getLabel();
                    }

                    return $options;
                })
                ->inline()
                ->live()
                ->dehydrated(false)
                ->required()
                ->afterStateHydrated(fn (Set $set, ?Campaign $record) => $set('group_ownership', self::defaultGroupOwnership($record)))
                ->afterStateUpdated(fn (Set $set) => $set('segment_id', null)),
            $segmentIdField,
        ];
    }

    /**
     * @return array<GroupModel>
     */
    private static function availablePopulationTypes(): array
    {
        return collect(GroupModel::cases())
            ->filter(fn (GroupModel $type) => auth()->user()->hasLicense($type->class()::getLicenseType()))
            ->values()
            ->all();
    }

    /**
     * @param  array<GroupModel>  $availablePopulationTypes
     */
    private static function defaultPopulationType(?Campaign $record, array $availablePopulationTypes): ?string
    {
        $currentType = $record?->group->model;

        if ($currentType && in_array($currentType, $availablePopulationTypes, true)) {
            return $currentType->value;
        }

        $default = GroupModel::default();

        if (in_array($default, $availablePopulationTypes, true)) {
            return $default->value;
        }

        return ($availablePopulationTypes[0] ?? null)?->value;
    }

    private static function authoritativePopulationType(?Campaign $record, Get $get): ?string
    {
        return $record?->group->model->value ?? $get('population_type');
    }

    private static function defaultGroupOwnership(?Campaign $record): string
    {
        $group = $record?->group;

        if (! $group) {
            return GroupOwnership::Mine->value;
        }

        if ($group->user_id === auth()->id()) {
            return GroupOwnership::Mine->value;
        }

        $currentUserDepartmentId = auth()->user()?->team_id;

        if (filled($currentUserDepartmentId) && $group->user?->team_id === $currentUserDepartmentId) {
            return GroupOwnership::Department->value;
        }

        return GroupOwnership::All->value;
    }
}
