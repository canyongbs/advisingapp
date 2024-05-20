<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Ai\Filament\Resources\PromptTypeResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use AdvisingApp\Ai\Models\Prompt;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\CreateAction;
use AdvisingApp\Ai\Filament\Resources\PromptResource;
use Filament\Resources\RelationManagers\RelationManager;
use AdvisingApp\Ai\Filament\Resources\PromptResource\Pages\EditPrompt;
use AdvisingApp\Ai\Filament\Resources\PromptResource\Pages\ViewPrompt;
use AdvisingApp\Ai\Filament\Resources\PromptResource\Pages\ListPrompts;

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
