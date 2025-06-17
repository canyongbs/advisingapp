<?php

namespace AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource\Pages;

use AdvisingApp\Ai\Enums\AiModel;
use AdvisingApp\Ai\Enums\AiModelApplicabilityFeature;
use AdvisingApp\Ai\Filament\Resources\QnAAdvisorResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\Rule;

class EditQnAAdvisor extends EditRecord
{
    protected static string $resource = QnAAdvisorResource::class;

     protected static ?string $navigationLabel = 'Edit';

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
                            ->reactive()
                            ->options(AiModelApplicabilityFeature::CustomAdvisors->getModelsAsSelectOptions())
                            ->searchable()
                            ->required()
                            ->rule(Rule::enum(AiModel::class)->only(AiModelApplicabilityFeature::CustomAdvisors->getModels())),
                    ])
                    ->columns(2),
                    Textarea::make('description')
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
                    $assistant = $this->getRecord();
                    $assistant->archived_at = now();
                    $assistant->save();

                    Notification::make()
                        ->title('QnA Advisor archived')
                        ->success()
                        ->send();
                })
                ->hidden(fn (): bool => (bool) $this->getRecord()->archived_at),
            Action::make('restore')
                ->action(function () {
                    $assistant = $this->getRecord();
                    $assistant->archived_at = null;
                    $assistant->save();

                    Notification::make()
                        ->title('QnA Advisor restored')
                        ->success()
                        ->send();
                })
                ->hidden(function (): bool {
                    if (! $this->getRecord()->archived_at) {
                        return true;
                    }

                    return false;
                }),
            DeleteAction::make(),
        ];
    }
}
