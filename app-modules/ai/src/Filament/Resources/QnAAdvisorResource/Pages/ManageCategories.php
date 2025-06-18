<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Models\QnAAdvisor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\AssociateAction;
use Filament\Tables\Actions\AttachAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\DetachAction;
use Filament\Tables\Actions\DetachBulkAction;
use Filament\Tables\Actions\DissociateAction;
use Filament\Tables\Actions\DissociateBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ManageCategories extends ManageRelatedRecords
{
    protected static string $resource = QnAAdvisorResource::class;

    protected static ?string $title = 'Manage Categories';

    protected static string $relationship = 'categories';

    protected static ?string $navigationGroup = 'Configuration';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->unique(
                        table: 'qn_a_advisor_categories',
                        column: 'name',
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule) {
                            /** @var QnAAdvisor $advisor */
                            $advisor = $this->getOwnerRecord();

                            $rule->where('qn_a_advisor_id', $advisor->getKey());
                        }
                    )
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->required()
                    ->string()
                    ->maxLength(65535)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('description')
                ->limit(50)
                ->wrap(),
            ])
            ->headerActions([
                CreateAction::make()
                    ->modalHeading('Create QnA Advisor Category'),
            ])
            ->actions([
                EditAction::make(),
            ])
            ->emptyStateHeading('No QnA Advisor Categories Found')
            ->emptyStateDescription('');
    }
}
