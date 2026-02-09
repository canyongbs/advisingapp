<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Division\Models\Division;
use AdvisingApp\ResourceHub\Filament\Actions\DraftResourceHubArticleWithAiAction;
use AdvisingApp\ResourceHub\Filament\Resources\ResourceHubArticles\ResourceHubArticleResource;
use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use AdvisingApp\ResourceHub\Models\ResourceHubQuality;
use AdvisingApp\ResourceHub\Models\ResourceHubStatus;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Actions\Action as BaseAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Model;

class EditResourceHubArticle extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = ResourceHubArticleResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->tabs([
                        Tab::make('Content')
                            ->label('Resource')
                            ->schema([
                                TiptapEditor::make('article_details')
                                    ->hiddenLabel()
                                    ->columnSpanFull()
                                    ->extraInputAttributes([
                                        'style' => 'min-height: 32rem;',
                                        'class' => 'text-gray-900 dark:bg-gray-800 dark:text-gray-100 border-2 dark:border-0 border-gray-200 rounded-none mx-4 my-2 px-8 py-4',
                                    ]),
                                Actions::make([
                                    DraftResourceHubArticleWithAiAction::make(),
                                ])
                                    ->visible(
                                        auth()->user()->hasLicense(LicenseType::ConversationalAi)
                                    ),
                            ]),
                        Tab::make('Properties')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Article Title')
                                    ->required()
                                    ->string()
                                    ->suffixAction(
                                        BaseAction::make('saveArticleTitle')
                                            ->icon('heroicon-o-check')
                                            ->action(function (Model $record, $state) {
                                                if ($record->title === $state) {
                                                    return;
                                                }

                                                $record->update([
                                                    'title' => $state,
                                                ]);

                                                if ($record->wasChanged('title')) {
                                                    Notification::make()
                                                        ->title("Title successfully updated to '{$record->title}'")
                                                        ->success()
                                                        ->duration(3000)
                                                        ->send();
                                                }
                                            }),
                                    )
                                    ->columnSpanFull(),
                                Textarea::make('notes')
                                    ->label('Notes')
                                    ->columnSpanFull()
                                    ->extraInputAttributes(['style' => 'min-height: 12rem;']),
                                Toggle::make('public')
                                    ->label('Public')
                                    ->default(false)
                                    ->onColor('success')
                                    ->offColor('gray'),
                                Toggle::make('has_table_of_contents')
                                    ->label('Table of Contents')
                                    ->default(false)
                                    ->onColor('success')
                                    ->offColor('gray'),
                            ])
                            ->columns(2),
                        Tab::make('Metadata')
                            ->schema([
                                Select::make('status_id')
                                    ->label('Status')
                                    ->relationship('status', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->exists((new ResourceHubStatus())->getTable(), (new ResourceHubStatus())->getKeyName()),
                                Select::make('quality_id')
                                    ->label('Quality')
                                    ->relationship('quality', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->exists((new ResourceHubQuality())->getTable(), (new ResourceHubQuality())->getKeyName()),
                                Select::make('category_id')
                                    ->label('Category')
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->exists((new ResourceHubCategory())->getTable(), (new ResourceHubCategory())->getKeyName()),
                                Select::make('division')
                                    ->label('Division')
                                    ->multiple()
                                    ->relationship('division', 'name')
                                    ->searchable(['name', 'code'])
                                    ->preload()
                                    ->visible(fn (): bool => Division::count() > 1)
                                    ->exists((new Division())->getTable(), (new Division())->getKeyName()),
                                Section::make()
                                    ->schema([
                                        Select::make('manager_ids')
                                            ->label('Managers')
                                            ->relationship('managers', 'name')
                                            ->multiple()
                                            ->searchable()
                                            ->preload()
                                            ->exists('users', 'id'),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Article details successfully saved';
    }

    protected function getFormActions(): array
    {
        return [
            BaseAction::make('cancel')
                ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Discard Changes?')
                ->modalDescription('You have unsaved changes to this Resource Hub article. If you cancel now, all changes made during this editing session will be lost. This action cannot be undone.')
                ->modalSubmitActionLabel('Discard Changes')
                ->modalCancelActionLabel('Continue Editing')
                ->action(fn () => $this->redirect(ResourceHubArticleResource::getUrl('view', ['record' => $this->record]))),
            $this->getSubmitFormAction()->label('Save'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            BaseAction::make('save')
                ->action('save')
                ->button()
                ->color('primary')
                ->label('Save'),
        ];
    }
}
