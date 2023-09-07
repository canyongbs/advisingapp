<?php

namespace Assist\AssistDataModel\Filament\Resources\StudentResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Forms\Components\Hidden;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\Component;
use Filament\Tables\Actions\CreateAction;
use Assist\AssistDataModel\Models\Student;
use Filament\Infolists\Components\Fieldset;
use Filament\Forms\Components\MorphToSelect;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\RelationManagers\RelationManager;
use Assist\Interaction\Filament\Resources\InteractionResource\Pages\CreateInteraction;

// TODO Continue work on the infolist and table for the InteractionsRelationManager
// Then, copy all of this over to the ProspectResource and ServiceRequestResource
// There is probably a way to do this without repeating a bunch of work
class InteractionsRelationManager extends RelationManager
{
    protected static string $relationship = 'interactions';

    public function form(Form $form): Form
    {
        $createInteractionForm = (resolve(CreateInteraction::class))->form($form);

        $formComponents = collect($createInteractionForm->getComponents())->filter(function (Component $component) {
            if (! $component instanceof MorphToSelect) {
                return true;
            }
        })->toArray();

        return parent::form($createInteractionForm)
            ->schema([
                Hidden::make('interactable_id')
                    ->default($this->ownerRecord->identifier()),
                Hidden::make('interactable_type')
                    ->default(resolve(Student::class)->getMorphClass()),
                ...$formComponents,
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('user.name')
                    ->label('Created By'),

                Fieldset::make('Details')
                    ->schema([
                        TextEntry::make('campaign.name'),
                        TextEntry::make('driver.name'),
                        TextEntry::make('outcome.name'),
                        TextEntry::make('status.name'),
                        TextEntry::make('type.name'),
                    ]),
                Fieldset::make('Time')
                    ->schema([
                        TextEntry::make('start_datetime'),
                        TextEntry::make('end_datetime'),
                    ]),
                Fieldset::make('Notes')
                    ->schema([
                        TextEntry::make('subject'),
                        TextEntry::make('description'),
                    ]),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('id'),
                TextColumn::make('campaign.name'),
                TextColumn::make('driver.name'),
                TextColumn::make('outcome.name'),
                TextColumn::make('status.name'),
                TextColumn::make('type.name'),
                TextColumn::make('subject'),
                TextColumn::make('description'),
            ])
            ->filters([
            ])
            ->headerActions([
                CreateAction::make(),
            ])
            ->actions([
                ViewAction::make(),
            ])
            ->bulkActions([
            ])
            ->emptyStateActions([
                CreateAction::make(),
            ]);
    }
}
