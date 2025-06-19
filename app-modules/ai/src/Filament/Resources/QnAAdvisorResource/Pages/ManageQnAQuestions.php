<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Models\QnAAdvisor;
use App\Features\QnAAdvisorFeature;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ManageQnAQuestions extends ManageRelatedRecords
{
    protected static string $resource = QnAAdvisorResource::class;

    protected static string $relationship = 'questions';

    protected static ?string $title = 'Questions';

    protected static ?string $navigationGroup = 'Configuration';

    public static function canAccess(array $parameters = []): bool
    {
        return QnAAdvisorFeature::active() && parent::canAccess($parameters);
    }

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

    /**
    * @return array<int|string, string|null>
    */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var QnAAdvisor $record */
        $record = $this->getRecord();

        /** @var array<string, string> $breadcrumbs */
        $breadcrumbs = [
            $resource::getUrl() => $resource::getBreadcrumb(),
            $resource::getUrl('view', ['record' => $record]) => Str::limit($record->name, 16),
            ...(filled($breadcrumb = $this->getBreadcrumb()) ? [$breadcrumb] : []),
        ];

        if (filled($cluster = static::getCluster())) {
            return $cluster::unshiftClusterBreadcrumbs($breadcrumbs);
        }

        return $breadcrumbs;
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('question')
            ->columns([
                TextColumn::make('question')
                    ->searchable(),
                TextColumn::make('answer')
                    ->limit(50)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('category.name')
                    ->searchable(),

            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', modifyQueryUsing: function ($query) {
                        /** @var QnAAdvisor $advisor */
                        $advisor = $this->getOwnerRecord();
                        $query->where('qn_a_advisor_id', $advisor->getKey());
                    })
                    ->multiple()
                    ->preload()
                    ->searchable(),
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
            ])
            ->emptyStateHeading('No QnA Advisor Questions Found')
            ->emptyStateDescription('');
    }
}
