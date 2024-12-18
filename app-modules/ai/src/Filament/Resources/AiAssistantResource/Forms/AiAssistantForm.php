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

namespace AdvisingApp\Ai\Filament\Resources\AiAssistantResource\Forms;

use AdvisingApp\Ai\Enums\AiApplication;
use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Models\AiAssistant;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Validation\Rule;

class AiAssistantForm
{
    public function form(Form | Component $form): Form | Component
    {
        return $form
            ->schema([
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
                TextInput::make('name')
                    ->required()
                    ->string()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Select::make('application')
                    ->options(AiApplication::class)
                    ->default(AiApplication::getDefault())
                    ->live()
                    ->afterStateUpdated(fn (Set $set, $state) => filled(AiApplication::parse($state)) ? $set('model', AiApplication::parse($state)->getDefaultModel()->value) : null)
                    ->required()
                    ->enum(AiApplication::class)
                    ->columnStart(1)
                    ->disabledOn('edit'),
                Select::make('model')
                    ->reactive()
                    ->options(
                        fn (Get $get): array => filled(AiApplication::parse($get('application')))
                            ? collect(AiApplication::parse($get('application'))
                                ->getModels())
                                ->mapWithKeys(fn (AiModel $model): array => [$model->value => $model->getLabel()])
                                ->all()
                            : []
                    )
                    ->searchable()
                    ->required()
                    ->rules(
                        fn (Get $get): array => filled(AiApplication::parse($get('application')))
                            ? [
                                Rule::enum(AiModel::class)
                                    ->only(
                                        AiApplication::parse($get('application'))
                                            ->getModels()
                                    ),
                            ]
                            : []
                    )
                    ->visible(fn (Get $get): bool => filled($get('application'))),
                Textarea::make('description')
                    ->columnSpanFull()
                    ->required(),
                Section::make('Configure AI Assistant')
                    ->description('The following information will be used to instruct your AI Assistant on how to respond.')
                    ->schema([
                        Textarea::make('instructions')
                            ->helperText('Instructions are used to provide context to the AI Assistant on how to respond to user queries.')
                            ->reactive()
                            ->required()
                            ->maxLength(fn (Get $get): int => (AiModel::parse($get('model')) ?? AiModel::OpenAiGpt35)->getService()->getMaxAssistantInstructionsLength()),
                    ]),
                Section::make('Additional Knowledge')
                    ->description('Add additional knowledge to your custom AI Assistant to improve its responses.')
                    ->reactive()
                    ->columns([
                        'sm' => 1,
                        'md' => 2,
                    ])
                    ->hidden(function (?AiAssistant $record, Get $get) {
                        if (is_null($record)) {
                            return true;
                        }

                        if ($record->isDefault()) {
                            return true;
                        }
                        $model = $get('model');

                        if (blank($model)) {
                            return true;
                        }

                        return ! AiModel::parse($model)->supportsAssistantFileUploads();
                    })
                    ->schema([
                        Repeater::make('files')
                            ->relationship()
                            ->hiddenLabel()
                            ->simple(
                                TextInput::make('name')
                                    ->disabled(),
                            )
                            ->addable(false)
                            ->visible(fn (?AiAssistant $record): bool => $record?->files->isNotEmpty() ?? false)
                            ->deleteAction(
                                fn (Action $action) => $action->requiresConfirmation()
                                    ->modalHeading('Are you sure you want to delete this file?')
                                    ->modalDescription('This file will be permanently removed from the assistant, and cannot be restored.')
                            ),
                        FileUpload::make('uploaded_files')
                            ->hiddenLabel()
                            ->multiple()
                            ->reactive()
                            ->maxFiles(fn (?AiAssistant $record): int => 3 - $record?->files->count() ?? 0)
                            ->disabled(fn (?AiAssistant $record): int => $record?->files->count() === 3)
                            ->acceptedFileTypes(config('ai.supported_file_types'))
                            ->storeFiles(false)
                            ->helperText(function (?AiAssistant $record): string {
                                if ($record?->files->count() < 3) {
                                    return 'You may upload a total of 3 files to your custom assistant. Files must be less than 256mb.';
                                }

                                return "You've reached the maximum file upload limit of 3 for custom assistants. Please delete a file if you wish to upload another.";
                            })
                            ->maxSize(256)
                            ->columnSpan(function (Get $get) {
                                $files = $get('files');
                                $firstFile = reset($files);

                                if (! $firstFile || blank($firstFile['name'])) {
                                    return 'full';
                                }

                                return 1;
                            }),
                    ]),
            ]);
    }
}
