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

namespace AdvisingApp\Authorization\Filament\Resources\UserGroups\RelationManagers;

use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Authorization\Models\UserGroup;
use AdvisingApp\Authorization\Rules\RoleIsAssignableToUserGroup;
use App\Features\UserGroupsFeature;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Authenticatable;
use Filament\Actions\AttachAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DetachAction;
use Filament\Actions\DetachBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'name';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        // TODO: Cleanup Task (user-groups-feature): remove feature-flag gating once User Groups is permanent.
        return UserGroupsFeature::active()
            && parent::canViewForRecord($ownerRecord, $pageClass)
            && (auth()->user()?->can('view', $ownerRecord) ?? false);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->authorize(fn (): bool => $this->canUpdateOwnerGroup())
                    ->label('Associate roles')
                    ->schema([
                        Select::make('recordId')
                            ->label('Roles')
                            ->hiddenLabel()
                            ->multiple()
                            ->searchable()
                            ->required()
                            ->preload()
                            ->nestedRecursiveRules([new RoleIsAssignableToUserGroup()])
                            ->getSearchResultsUsing(
                                fn (string $search): array => $this->getAssignableRolesQuery()
                                    ->where(new Expression('lower(name)'), 'like', '%' . Str::lower($search) . '%')
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all()
                            )
                            ->options(
                                fn (): array => $this->getAssignableRolesQuery()
                                    ->limit(50)
                                    ->pluck('name', 'id')
                                    ->all()
                            )
                            ->getOptionLabelsUsing(
                                fn (array $values): array => $this->getAssignableRolesQuery()
                                    ->whereKey($values)
                                    ->pluck('name', 'id')
                                    ->all()
                            ),
                    ])
                    ->multiple()
                    ->preloadRecordSelect(),
            ])
            ->recordActions([
                DetachAction::make()
                    ->authorize(fn (): bool => $this->canUpdateOwnerGroup()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DetachBulkAction::make()
                        ->authorize(fn (): bool => $this->canUpdateOwnerGroup()),
                ]),
            ]);
    }

    protected function canUpdateOwnerGroup(): bool
    {
        return auth()->user()?->can('update', $this->getOwnerRecord()) ?? false;
    }

    /**
     * @return Builder<Role>
     */
    protected function getAssignableRolesQuery(): Builder
    {
        $userGroup = $this->getOwnerRecord();

        assert($userGroup instanceof UserGroup);

        return Role::query()
            ->where('guard_name', 'web')
            ->whereNotIn('name', Authenticatable::ADMIN_ROLES)
            ->whereKeyNot($userGroup->roles()->pluck('roles.id')->all())
            ->orderBy('name');
    }
}
