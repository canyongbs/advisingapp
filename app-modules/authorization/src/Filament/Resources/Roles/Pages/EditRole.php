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

namespace AdvisingApp\Authorization\Filament\Resources\Roles\Pages;

use AdvisingApp\Authorization\Enums\PermissionDescription;
use AdvisingApp\Authorization\Filament\Resources\Roles\Pages\Concerns\HasGuardAwareRoleUsers;
use AdvisingApp\Authorization\Filament\Resources\Roles\RoleResource;
use AdvisingApp\Authorization\Models\PermissionGroup;
use AdvisingApp\Authorization\Models\Role;
use AdvisingApp\Authorization\Models\UserGroup;
use App\Enums\Feature;
use App\Features\UserGroupsFeature;
use App\Models\Authenticatable;
use App\Rules\OnlySuperAdminMayAssignAdminRoles;
use CanyonGBS\Common\Filament\Forms\Components\PermissionsMatrix;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Unique;

class EditRole extends EditRecord
{
    use HasGuardAwareRoleUsers;

    protected static string $resource = RoleResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Properties')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(125)
                            ->live(onBlur: true)
                            ->unique(
                                table: 'roles',
                                column: 'name',
                                ignoreRecord: true,
                                modifyRuleUsing: function (Unique $rule): void {
                                    /** @var Role $role */
                                    $role = $this->record;

                                    $rule->where('guard_name', $role->guard_name);
                                }
                            ),
                        ToggleButtons::make('guard_name')
                            ->label('Access Control')
                            ->options([
                                'web' => 'Console',
                                'api' => 'API',
                            ])
                            ->inline()
                            ->disabled()
                            ->dehydrated(),
                        Textarea::make('description')
                            ->nullable()
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ]),
                Section::make('Membership')
                    ->columns(2)
                    ->schema([
                        Select::make('users')
                            ->relationship('users', 'name')
                            ->preload()
                            ->label('Users')
                            ->multiple()
                            ->searchable()
                            ->options(fn (Get $get): array => $this
                                ->getRoleUsersQuery((string) $get('guard_name'))
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->getSearchResultsUsing(fn (string $search, Get $get): array => $this
                                ->getRoleUsersQuery((string) $get('guard_name'), $search)
                                ->limit(50)
                                ->pluck('name', 'id')
                                ->all())
                            ->getOptionLabelsUsing(fn (array $values, Get $get): array => $this
                                ->getRoleUsersQuery((string) $get('guard_name'))
                                ->whereKey($values)
                                ->pluck('name', 'id')
                                ->all())
                            ->rule(fn (Get $get) => $this->getRoleUsersValidationRule($get)),
                        Select::make('userGroups')
                            ->relationship(
                                'userGroups',
                                'name',
                                fn (Builder $query) => $query
                                // @phpstan-ignore method.notFound
                                    ->withoutArchived()
                            )
                            ->preload()
                            ->searchable()
                            ->label('Groups')
                            ->multiple()
                            ->exists((new UserGroup())->getTable(), 'id')
                            // TODO: Cleanup Task (user-groups-feature): remove this visibility and conditional state-loading branch when User Groups is made permanent.
                            ->visible(fn (Get $get): bool => UserGroupsFeature::active() && $this->canAssignUserGroupsToRole((string) $get('guard_name'), $get('name')))
                            ->loadStateFromRelationshipsUsing(function (Select $component): void {
                                if (UserGroupsFeature::active()) {
                                    $component->fillStateFromRelationship();
                                }
                            }),
                    ]),
                PermissionsMatrix::make('permissions')
                    ->columnSpanFull()
                    ->guard(fn (Get $get): string => $get('guard_name'))
                    ->descriptions(PermissionDescription::cases())
                    ->permissionGroupModel(PermissionGroup::class)
                    ->hiddenPermissionGroups(fn (): array => Feature::getDisabledPermissionGroupNames()),
            ]);
    }

    protected function beforeSave(): void
    {
        /** @var Role $role */
        $role = $this->record;

        $this->ensureUserGroupsCanBeAssigned(
            $role->guard_name,
            isset($this->data['name']) ? (string) $this->data['name'] : $role->name,
            (array) ($this->data['userGroups'] ?? []),
        );

        $user = Auth::user();

        assert($user instanceof Authenticatable);

        if ($user->isSuperAdmin()) {
            return;
        }

        $submittedRoleName = isset($this->data['name']) ? (string) $this->data['name'] : $role->name;
        $isCurrentAdminRole = in_array($role->name, Authenticatable::ADMIN_ROLES, true);
        $isSubmittedAdminRole = in_array($submittedRoleName, Authenticatable::ADMIN_ROLES, true);
        if ($submittedRoleName !== $role->name && ($isCurrentAdminRole || $isSubmittedAdminRole)) {
            throw ValidationException::withMessages([
                'data.name' => 'Only super admins may rename roles to or from an admin role name.',
            ]);
        }
        if (! $isCurrentAdminRole) {
            return;
        }

        if (! $this->hasMembershipChanges($role)) {
            return;
        }

        $validator = Validator::make(
            ['users' => $role->getKey()],
            ['users' => [new OnlySuperAdminMayAssignAdminRoles()]],
        );

        if ($validator->fails()) {
            $message = $validator->errors()->first('users');

            throw ValidationException::withMessages([
                'data.users' => $message,
                'data.userGroups' => $message,
            ]);
        }
    }

    private function hasMembershipChanges(Role $role): bool
    {
        $submittedUserIds = $this->normalizeIds($this->data['users'] ?? []);
        $currentUserIds = $this->normalizeIds(
            $role->users()
                ->pluck($role->users()->getRelated()->getQualifiedKeyName())
                ->all(),
        );

        if ($submittedUserIds !== $currentUserIds) {
            return true;
        }

        if (! UserGroupsFeature::active()) {
            return false;
        }

        $submittedGroupIds = $this->normalizeIds($this->data['userGroups'] ?? []);
        $currentGroupIds = $this->normalizeIds(
            $role->userGroups()
                ->pluck($role->userGroups()->getRelated()->getQualifiedKeyName())
                ->all(),
        );

        return $submittedGroupIds !== $currentGroupIds;
    }

    /**
     * @param  array<mixed>  $ids
     * @return array<string>
     */
    private function normalizeIds(array $ids): array
    {
        return collect($ids)
            ->filter()
            ->map(fn (mixed $id): string => (string) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
