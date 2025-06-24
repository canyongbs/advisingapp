<?php

namespace AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource\Pages;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Filament\Resources\QnaAdvisorResource;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Ai\Settings\AiQnaAdvisorSettings;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Actions\Action;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class EditQnaAdvisor extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = QnaAdvisorResource::class;

    protected static ?string $navigationLabel = 'Edit';

    protected static ?string $navigationGroup = 'QnA Advisor';

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
                            ->visible(auth()->user()->isSuperAdmin())
                            ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::QuestionAndAnswerAdvisor->getModels()))
                            ->disabled(fn (): bool => ! app(AiQnaAdvisorSettings::class)->allow_selection_of_model)
                            ->default(function () {
                                $settings = app(AiQnaAdvisorSettings::class);

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
                    Section::make('Configure AI Advisor')
                        ->description('Design the capability of your advisor by including detailed instructions below.')
                        ->visible(auth()->user()->isSuperAdmin())
                        ->schema([
                            Textarea::make('instructions')
                                ->required()
                                ->visible(auth()->user()->isSuperAdmin())
                                ->disabled(fn (): bool => ! app(AiQnaAdvisorSettings::class)->allow_selection_of_model)
                                ->maxLength(fn (Get $get): int => (AiModel::parse($get('model')) ?? AiModel::OpenAiGpt4o)->getService()->getMaxAssistantInstructionsLength())
                                ->default(function () {
                                    $settings = app(AiQnaAdvisorSettings::class);

                                    if ($settings->allow_selection_of_model) {
                                        return null;
                                    }

                                    return $settings->instructions;
                                }),
                        ]),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('archive')
                ->color('danger')
                ->action(function () {
                    /** @var QnaAdvisor $qnqAdvisor */
                    $qnqAdvisor = $this->getRecord();
                    $qnqAdvisor->archived_at = now();
                    $qnqAdvisor->save();

                    Notification::make()
                        ->title('QnA Advisor archived')
                        ->success()
                        ->send();
                })
                ->hidden(fn (QnaAdvisor $record): bool => (bool) $record->archived_at),
            Action::make('restore')
                ->action(function () {
                    /** @var QnaAdvisor $qnqAdvisor */
                    $qnqAdvisor = $this->getRecord();
                    $qnqAdvisor->archived_at = null;
                    $qnqAdvisor->save();

                    Notification::make()
                        ->title('QnA Advisor restored')
                        ->success()
                        ->send();
                })
                ->hidden(function (QnaAdvisor $record): bool {
                    if (! $record->archived_at) {
                        return true;
                    }

                    return false;
                }),
        ];
    }
}
