<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Actions;
use Filament\Forms\Form;
use Assist\Division\Models\Division;
use Filament\Forms\Components\Radio;
use App\Filament\Fields\TiptapEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\EditRecord;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class EditKnowledgeBaseItem extends EditRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('question')
                    ->label('Question/Issue/Feature')
                    ->translateLabel()
                    ->required()
                    ->string(),
                Select::make('quality_id')
                    ->label('Quality')
                    ->translateLabel()
                    ->relationship('quality', 'name')
                    ->searchable()
                    ->preload()
                    ->exists((new KnowledgeBaseQuality())->getTable(), (new KnowledgeBaseQuality())->getKeyName()),
                Select::make('status_id')
                    ->label('Status')
                    ->relationship('status', 'name')
                    ->searchable()
                    ->preload()
                    ->exists((new KnowledgeBaseStatus())->getTable(), (new KnowledgeBaseStatus())->getKeyName()),
                Select::make('category_id')
                    ->label('Category')
                    ->translateLabel()
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload()
                    ->exists((new KnowledgeBaseCategory())->getTable(), (new KnowledgeBaseCategory())->getKeyName()),
                Radio::make('public')
                    ->label('Public')
                    ->translateLabel()
                    ->boolean()
                    ->default(false)
                    ->rules(['boolean']),
                Select::make('division')
                    ->label('Division')
                    ->translateLabel()
                    ->relationship('division', 'name')
                    ->searchable(['name', 'code'])
                    ->preload()
                    ->exists((new Division())->getTable(), (new Division())->getKeyName()),
                TiptapEditor::make('solution')
                    ->directory('kb-images')
                    ->label('Solution')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->string(),
                TiptapEditor::make('notes')
                    ->directory('kb-images')
                    ->label('Notes')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->string(),
            ]);
    }

    public function afterSave(): void
    {
        $this->record = $this->getRecord()->fresh();

        $this->fillForm();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
