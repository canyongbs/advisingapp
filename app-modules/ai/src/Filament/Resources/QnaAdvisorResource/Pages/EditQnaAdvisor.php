<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('archive')
                ->color('danger')
                ->action(function () {
                    /** @var QnaAdvisor $qnaAdvisor */
                    $qnaAdvisor = $this->getRecord();
                    $qnaAdvisor->archived_at = now();
                    $qnaAdvisor->save();

                    Notification::make()
                        ->title('QnA Advisor archived')
                        ->success()
                        ->send();
                })
                ->hidden(fn (QnaAdvisor $record): bool => (bool) $record->archived_at),
            Action::make('restore')
                ->action(function () {
                    /** @var QnaAdvisor $qnaAdvisor */
                    $qnaAdvisor = $this->getRecord();
                    $qnaAdvisor->archived_at = null;
                    $qnaAdvisor->save();

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
