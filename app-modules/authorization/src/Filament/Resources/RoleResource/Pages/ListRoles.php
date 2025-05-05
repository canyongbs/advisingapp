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

namespace AdvisingApp\Authorization\Filament\Resources\RoleResource\Pages;

use AdvisingApp\Authorization\Filament\Resources\RoleResource;
use AdvisingApp\Authorization\Models\Role;
use App\Filament\Tables\Columns\IdColumn;
use App\Models\Authenticatable;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Throwable;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    public function getTabs(): array
    {
        return [
            'web' => Tab::make('Web')
                ->modifyQueryUsing(fn (Builder $query) => $query->web()),
            'api' => Tab::make('Api')
                ->modifyQueryUsing(fn (Builder $query) => $query->api()),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                /** @var User $user */
                $user = auth()->user();

                if (! $user?->isSuperAdmin()) {
                    $query->where('name', '!=', Authenticatable::SUPER_ADMIN_ROLE);
                }
            })
            ->columns([
                IdColumn::make(),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('guard_name')
                    ->searchable(),
                TextColumn::make('users_count')
                    ->label('Users')
                    ->counts('users')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Action::make('duplicateRole')
                    ->label('Duplicate')
                    ->icon('heroicon-o-document-duplicate')
                    ->requiresConfirmation()
                    ->modalHeading('Duplicate Role')
                    ->modalSubmitActionLabel('Duplicate')
                    ->form([
                        TextInput::make('name')
                            ->label('Name')
                            ->required(),
                    ])
                    ->action(function (Role $record, array $data) {
                        try {
                            DB::beginTransaction();

                            $newRole = Role::create([
                                'name' => $data['name'],
                                'guard_name' => $record->guard_name,
                            ]);

                            $newRole->syncPermissions($record->permissions);

                            DB::commit();

                            Notification::make()
                                ->title('Role duplicated successfully.')
                                ->success()
                                ->send();

                            return redirect(EditRole::getUrl(['record' => $newRole]));
                        } catch (Throwable $e) {
                            DB::rollback();
                            Notification::make()
                                ->title('Failed to duplicate role.')
                                ->danger()
                                ->send();
                        }
                    }),
                ViewAction::make(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
