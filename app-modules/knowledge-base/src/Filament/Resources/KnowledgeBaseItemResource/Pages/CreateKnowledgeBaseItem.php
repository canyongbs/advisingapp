<?php

namespace Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource\Pages;

use Filament\Forms\Form;
use App\Models\Institution;
use Filament\Forms\Components\Radio;
use App\Filament\Fields\TiptapEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;
use Assist\KnowledgeBase\Filament\Resources\KnowledgeBaseItemResource;

class CreateKnowledgeBaseItem extends CreateRecord
{
    protected static string $resource = KnowledgeBaseItemResource::class;

    public function form(Form $form): Form
    {
        return parent::form($form)
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
                Select::make('institution')
                    ->label('Institution')
                    ->translateLabel()
                    ->relationship('institution', 'name')
                    ->searchable(['name', 'code'])
                    ->preload()
                    ->exists((new Institution())->getTable(), (new Institution())->getKeyName()),
                TiptapEditor::make('solution')
                    ->label('Solution')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->string(),
                TiptapEditor::make('notes')
                    ->label('Notes')
                    ->translateLabel()
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->string(),
            ]);
    }
}
