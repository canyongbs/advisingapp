<?php

namespace AdvisingApp\Assistant\Filament\Resources\PromptTypeResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use AdvisingApp\Assistant\Models\Prompt;
use Filament\Tables\Actions\CreateAction;
use AdvisingApp\Assistant\Filament\Resources\PromptResource;
use App\Filament\Resources\RelationManagers\RelationManager;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\EditPrompt;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\ViewPrompt;
use AdvisingApp\Assistant\Filament\Resources\PromptResource\Pages\ListPrompts;

class PromptsRelationManager extends RelationManager
{
    protected static string $relationship = 'prompts';

    public function infolist(Infolist $infolist): Infolist
    {
        return (new ViewPrompt())->infolist($infolist);
    }

    public function form(Form $form): Form
    {
        return (new EditPrompt())->form($form);
    }

    public function table(Table $table): Table
    {
        return (new ListPrompts())
            ->table($table)
            ->recordTitleAttribute('title')
            ->inverseRelationship('type')
            ->headerActions([
                CreateAction::make(),
            ]);
    }

    //If we want modals remove these configs or remove the pages
    protected function configureViewAction(ViewAction $action): void
    {
        parent::configureViewAction($action);

        if (PromptResource::hasPage('view')) {
            $action->url(fn (Prompt $record): string => PromptResource::getUrl('view', ['record' => $record]));
        }
    }

    protected function configureEditAction(EditAction $action): void
    {
        parent::configureEditAction($action);

        if (PromptResource::hasPage('edit')) {
            $action->url(fn (Prompt $record): string => PromptResource::getUrl('edit', ['record' => $record]));
        }
    }

    protected function configureCreateAction(CreateAction $action): void
    {
        parent::configureCreateAction($action);

        if (PromptResource::hasPage('create')) {
            $action->url(fn (): string => PromptResource::getUrl('create'));
        }
    }
}
