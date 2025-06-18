<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Models\QnAAdvisor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ManageQnAQuestions extends ManageRelatedRecords
{
    protected static string $resource = QnAAdvisorResource::class;

    protected static string $relationship = 'questions';

    protected static ?string $title = 'Manage Questions';

    protected static ?string $navigationGroup = 'Configuration';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', modifyQueryUsing: function ($query) {
                        /** @var QnAAdvisor $advisor */
                        $advisor = $this->getOwnerRecord();
                        $query->where('qn_a_advisor_id', $advisor->getKey());
                    })
                    ->required()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull(),
                TextInput::make('question')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('answer')
                    ->required()
                    ->string()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                TextColumn::make('question'),
                TextColumn::make('answer')
                    ->limit(50)
                    ->wrap(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Create QnA Advisor Question'),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                   DeleteBulkAction::make(),
                ]),
            ]);
    }
}
