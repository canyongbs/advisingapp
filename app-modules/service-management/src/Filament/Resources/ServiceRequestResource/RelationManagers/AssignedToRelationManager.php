<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestResource\RelationManagers;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Filament\Resources\UserResource;
use Assist\ServiceManagement\Models\ServiceRequest;
use Filament\Resources\RelationManagers\RelationManager;

class AssignedToRelationManager extends RelationManager
{
    protected static string $relationship = 'assignedTo';

    protected static ?string $recordTitleAttribute = 'name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Name'),
            ])
            ->paginated(false)
            ->headerActions([
                // TODO: Figure out how to make it so this only displays on the edit page
                Tables\Actions\Action::make('reassign-service-request')
                    ->label('Reassign Service Request')
                    ->color('gray')
                    ->action(function (array $data): void {
                        /** @var ServiceRequest $serviceRequest */
                        $serviceRequest = $this->ownerRecord;

                        $serviceRequest->assignedTo()->associate($data['userId'])->save();
                    })
                    ->form([
                        Forms\Components\Select::make('userId')
                            ->label('Assigned User')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => User::whereRaw('LOWER(name) LIKE ? ', ['%' . str($search)->lower() . '%'])->pluck('name', 'id')->toArray())
                            ->getOptionLabelUsing(fn ($value): ?string => User::find($value)?->name)
                            ->placeholder('Search for and select a User')
                            ->required(),
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->url(fn (User $user) => UserResource::getUrl('view', ['record' => $user])),
            ]);
    }
}
