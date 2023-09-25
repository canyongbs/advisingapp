<?php

namespace Assist\Interaction\Filament\Resources\InteractionResource\Pages;

use Assist\AssistDataModel\Models\Student;
use Assist\Interaction\Filament\Resources\InteractionResource\RelationManagers\HasManyMorphedInteractionsRelationManager;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Table;

class ManageInteractions extends ManageRelatedRecords
{
    protected static string $interactableType;

    protected static string $relationship = 'interactions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $breadcrumb = 'Interactions';

    // TODO: Automatically set from Filament based on relationship name
    protected static ?string $navigationLabel = 'Interactions';

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

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
                    ->default($this->getOwnerRecord()->identifier()),
                Hidden::make('interactable_type')
                    ->default(resolve(static::$interactableType)->getMorphClass()),
                ...$formComponents,
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return (resolve(HasManyMorphedInteractionsRelationManager::class))->infolist($infolist);
    }

    public function table(Table $table): Table
    {
        return (resolve(HasManyMorphedInteractionsRelationManager::class))->table($table);
    }
}
