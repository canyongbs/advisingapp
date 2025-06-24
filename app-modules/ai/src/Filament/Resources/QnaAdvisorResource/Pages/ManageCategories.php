<?php

namespace AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages;

use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Models\QnaAdvisor;
use App\Features\QnaAdvisorFeature;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;

class ManageCategories extends ManageRelatedRecords
{
    protected static string $resource = QnaAdvisorResource::class;

    protected static ?string $title = 'Categories';

    protected static string $relationship = 'categories';

    protected static ?string $navigationGroup = 'Configuration';

    public static function canAccess(array $parameters = []): bool
    {
        return QnaAdvisorFeature::active() && parent::canAccess($parameters);
    }

    /**
     * @return array<int|string, string|null>
     */
    public function getBreadcrumbs(): array
    {
        $resource = static::getResource();
        /** @var QnaAdvisor $record */
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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->unique(
                        table: 'qna_advisor_categories',
                        column: 'name',
                        ignoreRecord: true,
                        modifyRuleUsing: function (Unique $rule) {
                            /** @var QnaAdvisor $advisor */
                            $advisor = $this->getOwnerRecord();

                            $rule->where('qna_advisor_id', $advisor->getKey());
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
