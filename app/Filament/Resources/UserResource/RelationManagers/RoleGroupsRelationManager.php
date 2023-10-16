<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Filament\Columns\IdColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Concerns\InteractsWithActions;
use App\Filament\Resources\RelationManagers\RelationManager;

class RoleGroupsRelationManager extends RelationManager implements HasActions
{
    use InteractsWithActions;

    protected static ?string $label = 'Profile';

    protected static ?string $title = 'Profiles';

    protected static string $relationship = 'roleGroups';

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
            ])
            ->filters([
            ])
            ->headerActions([
                AttachAction::make()->recordTitle(function ($record) {
                    return Str::of($record->name);
                }),
            ])
            ->actions([
                DetachAction::make()
                    ->label(function ($record) {
                        return "Remove from {$record->name} Role Group";
                    })
                    ->requiresConfirmation()
                    ->modalDescription(function ($record) {
                        return "Are you sure you want to remove {$this->ownerRecord->name} from the {$record->name} Role Group?";
                    }),
            ])
            ->bulkActions([
            ]);
    }
}
