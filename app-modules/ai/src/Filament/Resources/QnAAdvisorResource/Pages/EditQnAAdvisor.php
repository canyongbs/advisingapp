<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use AdvisingApp\Ai\Models\QnAAdvisor;
use AdvisingApp\Ai\Settings\AiQnAAdvisorSettings;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EditQnAAdvisor extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = QnAAdvisorResource::class;

    protected static ?string $navigationLabel = 'Edit';

    protected static ?string $navigationGroup = 'QnA Advisor';

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

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    SpatieMediaLibraryFileUpload::make('avatar')
                        ->label('Avatar')
                        ->disk('s3')
                        ->collection('avatar')
                        ->visibility('private')
                        ->avatar()
                        ->columnSpanFull()
                        ->acceptedFileTypes([
                            'image/png',
                            'image/jpeg',
                            'image/gif',
                        ]),
                    Grid::make()->schema([
                        TextInput::make('name')
                            ->required()
                            ->string()
                            ->maxLength(255),
                        Select::make('model')
                            ->live()
                            ->options(AiModelApplicabilityFeature::QuestionAndAnswerAdvisor->getModelsAsSelectOptions())
                            ->searchable()
                            ->required()
                            ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::QuestionAndAnswerAdvisor->getModels()))
                            ->disabled(fn (): bool => ! app(AiQnAAdvisorSettings::class)->allow_selection_of_model)
                            ->default(function () {
                                $settings = app(AiQnAAdvisorSettings::class);

                                if ($settings->allow_selection_of_model) {
                                    return null;
                                }

                                return $settings->preselected_model;
                            }),
                    ])
                        ->columns(2),
                    Textarea::make('description')
                        ->maxLength(65535)
                        ->required(),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('archive')
                ->color('danger')
                ->action(function () {
                    /** @var QnAAdvisor $qnqAdvisor */
                    $qnqAdvisor = $this->getRecord();
                    $qnqAdvisor->archived_at = now();
                    $qnqAdvisor->save();

                    Notification::make()
                        ->title('QnA Advisor archived')
                        ->success()
                        ->send();
                })
                ->hidden(fn (QnAAdvisor $record): bool => (bool) $record->archived_at),
            Action::make('restore')
                ->action(function () {
                    /** @var QnAAdvisor $qnqAdvisor */
                    $qnqAdvisor = $this->getRecord();
                    $qnqAdvisor->archived_at = null;
                    $qnqAdvisor->save();

                    Notification::make()
                        ->title('QnA Advisor restored')
                        ->success()
                        ->send();
                })
                ->hidden(function (QnAAdvisor $record): bool {
                    if (! $record->archived_at) {
                        return true;
                    }

                    return false;
                }),
        ];
    }
}
