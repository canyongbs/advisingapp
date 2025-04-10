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

namespace App\Filament\Resources\UserResource\RelationManagers;

use AdvisingApp\Authorization\Models\Role;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Authenticatable;
use App\Rules\ExcludeSuperAdmin;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('guard_name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                IdColumn::make(),
                TextColumn::make('name'),
                TextColumn::make('guard_name'),
            ])
            ->headerActions([
                AttachAction::make()
                    ->form([
                        Select::make('recordId')
                            ->hiddenLabel()
                            ->searchable()
                            ->required()
                            ->rule(new ExcludeSuperAdmin())
                            ->preload()
                            ->getSearchResultsUsing(
                                fn (string $search): array => Role::query()->when(
                                    ! auth()->user()->isSuperAdmin(),
                                    fn (Builder $query) => $query->where('name', '!=', Authenticatable::SUPER_ADMIN_ROLE)
                                )
                                    ->where(new Expression('lower(name)'), 'like', '%' . strtolower($search) . '%')
                                    ->limit(50)->pluck('name', 'id')
                                    ->toArray()
                            )
                            ->options(function () {
                                /** @var User $user */
                                $user = $this->getOwnerRecord();

                                return Role::query()
                                    ->when(
                                        ! auth()->user()->isSuperAdmin(),
                                        fn (Builder $query) => $query->where('name', '!=', Authenticatable::SUPER_ADMIN_ROLE)
                                    )
                                    ->when(
                                        $user->has('roles'),
                                        fn (Builder $query) => $query->whereNotIn('id', $user->roles()->pluck('id')->toArray())
                                    )
                                    ->pluck('name', 'id')->toArray();
                            }),
                    ])
                    ->multiple()
                    ->preloadRecordSelect(),
            ])
            ->actions([
                DetachAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DetachBulkAction::make(),
                ]),
            ]);
    }
}
