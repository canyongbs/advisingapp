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

namespace AdvisingApp\Team\Filament\Resources\TeamResource\RelationManagers;

use App\Filament\Tables\Columns\IdColumn;
use App\Models\Scopes\WithoutSuperAdmin;
use App\Models\User;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
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
                TextColumn::make('email'),
            ])
            ->headerActions([
                AssociateAction::make()
                    ->label('Add user to this team')
                    ->recordSelectOptionsQuery(function (Builder $query) {
                        $query->tap(new WithoutSuperAdmin());
                    })
                    ->form(fn (AssociateAction $action): array => [
                        $action->getRecordSelect()
                            ->rules([
                                fn (): Closure => function (string $attribute, $value, Closure $fail) {
                                    //TODO: remove this if we support multiple teams
                                    if (User::findOrFail($value)->team) {
                                        $fail('This user already belongs to a team.');
                                    }

                                    //TODO: remove this if we want to allow super admin user as team member.
                                    if (User::findOrFail($value)->isSuperAdmin()) {
                                        $fail('Super admin users cannot be added to a team.');
                                    }
                                },
                            ]),
                    ]),
            ])
            ->actions([
                DissociateAction::make()
                    ->label('Remove from this team'),
            ])
            ->bulkActions([
            ]);
    }
}
