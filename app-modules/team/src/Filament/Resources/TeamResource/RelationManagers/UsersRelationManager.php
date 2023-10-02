<?php

namespace Assist\Team\Filament\Resources\TeamResource\RelationManagers;

use Closure;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use App\Filament\Resources\RelationManagers\RelationManager;

class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';

    protected static ?string $recordTitleAttribute = 'email';

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
                TextColumn::make('name'),
                TextColumn::make('email'),
            ])
            ->filters([
            ])
            ->headerActions([
                AttachAction::make()
                    ->label('Add user to this team')
                    //TODO: remove this if we support multiple teams
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->rules([
                                fn (): Closure => function (string $attribute, $value, Closure $fail) {
                                    if (User::findOrFail($value)->teams()->count() > 0) {
                                        $fail('This user already belongs to a team.');
                                    }
                                },
                            ]),
                    ]),
            ])
            ->actions([
                DetachAction::make()
                    ->label('Remove from this team'),
            ])
            ->bulkActions([
            ]);
    }
}
