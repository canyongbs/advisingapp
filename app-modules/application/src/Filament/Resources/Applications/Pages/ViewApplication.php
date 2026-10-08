<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Application\Filament\Resources\Applications\Pages;

use AdvisingApp\Application\Enums\ApplicationTab;
use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Form\Actions\GenerateSubmissibleEmbedCode;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use CanyonGBS\Common\Enums\Color as ColorEnum;
use CanyonGBS\Common\Filament\Actions\ArchiveAction;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Livewire\Attributes\Url;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    #[Url(as: 'tab')]
    public string $activeTab = ApplicationTab::View->value;

    public function mount(int | string $record): void
    {
        $requestedRecord = $record;
        $archivedApplication = Application::query()->whereKey($record)->whereNotNull('archived_at')->first();
        $record = $archivedApplication?->latestVersion()?->getRouteKey() ?? $record;

        parent::mount($record);

        $this->normalizeActiveTab();

        if ($requestedRecord !== $record) {
            $this->redirect(ApplicationResource::getUrl('view', [
                'record' => $this->getRecord(),
                'tab' => $this->activeTab,
            ]));
        }
    }

    public function updatedActiveTab(): void
    {
        $this->normalizeActiveTab();
    }

    /**
     * @return Schema
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Tabs::make()
                    ->columnSpanFull()
                    ->livewireProperty('activeTab')
                    ->tabs([
                        ApplicationTab::View->value => Tab::make(ApplicationTab::View->getLabel())
                            ->visible(fn (): bool => $this->isTabVisible(ApplicationTab::View))
                            ->schema(fn (): array => $this->isTabVisible(ApplicationTab::View) ? $this->viewFormSchema() : []),
                        ApplicationTab::Edit->value => Tab::make(ApplicationTab::Edit->getLabel())
                            ->view('application::filament.resources.applications.pages.persistent-form-tab')
                            ->visible(fn (): bool => $this->isTabVisible(ApplicationTab::Edit))
                            ->schema(fn (): array => $this->isTabVisible(ApplicationTab::Edit) ? [
                                Livewire::make(EditApplication::class, [
                                    'record' => $this->getRecord(),
                                ])->key('edit-application'),
                            ] : []),
                        ApplicationTab::Workflows->value => Tab::make(ApplicationTab::Workflows->getLabel())
                            ->visible(fn (): bool => $this->isTabVisible(ApplicationTab::Workflows))
                            ->schema(fn (): array => $this->relationManagerSchema(ApplicationTab::Workflows, ManageApplicationWorkflows::class)),
                        ApplicationTab::Submissions->value => Tab::make(ApplicationTab::Submissions->getLabel())
                            ->visible(fn (): bool => $this->isTabVisible(ApplicationTab::Submissions))
                            ->badge(fn (): ?string => $this->isTabVisible(ApplicationTab::Submissions) ? ManageApplicationSubmissions::getBadge($this->getRecord(), static::class) : null)
                            ->schema(fn (): array => $this->relationManagerSchema(ApplicationTab::Submissions, ManageApplicationSubmissions::class)),
                        ApplicationTab::Notifications->value => Tab::make(ApplicationTab::Notifications->getLabel())
                            ->view('application::filament.resources.applications.pages.persistent-form-tab')
                            ->visible(fn (): bool => $this->isTabVisible(ApplicationTab::Notifications))
                            ->schema(fn (): array => $this->isTabVisible(ApplicationTab::Notifications) ? [
                                Livewire::make(ManageApplicationNotifications::class, ['record' => $this->getRecord()])
                                    ->key('application-notifications'),
                            ] : []),
                    ]),
            ]);
    }

    public function hydrate(): void
    {
        parent::hydrate();

        $this->normalizeActiveTab();
    }

    protected function normalizeActiveTab(): void
    {
        if ($this->isActiveTabVisible()) {
            return;
        }

        $firstVisibleTab = collect(ApplicationTab::cases())
            ->first(fn (ApplicationTab $tab): bool => $this->isTabVisible($tab));

        abort_unless($firstVisibleTab !== null, 403);

        $this->activeTab = $firstVisibleTab->value;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->authorize(fn (): bool => $this->isTabVisible(ApplicationTab::View))
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn (Application $application) => route('applications.preview', $application))
                ->openUrlInNewTab(),
            Action::make('view')
                ->authorize(fn (): bool => $this->isTabVisible(ApplicationTab::View))
                ->url(fn (Application $application) => route('applications.show', ['application' => $application]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            Action::make('embed_snippet')
                ->authorize(fn (): bool => $this->isTabVisible(ApplicationTab::View))
                ->label('Embed Snippet')
                ->schema(
                    [
                        TextEntry::make('snippet')
                            ->label('Click to Copy')
                            ->state(function (Application $application) {
                                $code = resolve(GenerateSubmissibleEmbedCode::class)->handle($application);

                                $state = <<<EOD
                                ```
                                {$code}
                                ```
                                EOD;

                                return str($state)->markdown()->toHtmlString();
                            })
                            ->copyable()
                            ->copyableState(fn (Application $application) => resolve(GenerateSubmissibleEmbedCode::class)->handle($application))
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500)
                            ->extraAttributes(['class' => 'embed-code-snippet']),
                    ]
                )
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->hidden(fn (Application $application) => ! $application->embed_enabled),
            ArchiveAction::make()
                ->authorize(fn (): bool => ($this->isTabVisible(ApplicationTab::View) || $this->isTabVisible(ApplicationTab::Edit))
                    && ApplicationResource::canDelete($this->getRecord())),
        ];
    }

    protected function authorizeAccess(): void
    {
        abort_unless(collect(ApplicationTab::cases())->contains(fn (ApplicationTab $tab): bool => $this->isTabVisible($tab)), 403);
    }

    /**
     * @param class-string<RelationManager> $manager
     *
     * @return array<Livewire>
     */
    protected function relationManagerSchema(ApplicationTab $tab, string $manager): array
    {
        if ($this->activeTab !== $tab->value || ! $this->isTabVisible($tab)) {
            return [];
        }

        return [
            Livewire::make($manager, [
                'ownerRecord' => $this->getRecord(),
                'pageClass' => static::class,
            ])->key('application-' . $tab->value),
        ];
    }

    protected function isActiveTabVisible(): bool
    {
        return ($tab = ApplicationTab::tryFrom($this->activeTab)) instanceof ApplicationTab
            && $this->isTabVisible($tab);
    }

    protected function isTabVisible(ApplicationTab $tab): bool
    {
        $record = $this->getRecord();
        assert($record instanceof Application);

        return $tab->canAccess($record);
    }

    /** @return array<Component> */
    protected function viewFormSchema(): array
    {
        return [
            Section::make()
                ->columns()
                ->schema([
                    TextEntry::make('name'),
                    TextEntry::make('title'),
                    TextEntry::make('description')
                        ->columnSpanFull(),
                ]),
            Section::make('Configuration')
                ->columns()
                ->schema([
                    IconEntry::make('embed_enabled')
                        ->label('Embed Enabled')
                        ->boolean(),
                    TextEntry::make('allowed_domains')
                        ->visible(fn (Application $record) => $record->embed_enabled)
                        ->badge(),
                    IconEntry::make('should_generate_prospects')
                        ->label('Generate Prospects')
                        ->boolean(),
                    IconEntry::make('is_wizard')
                        ->label('Multi-step form')
                        ->boolean(),
                    IconEntry::make('allow_view_past_submissions')
                        ->label('Allow View Past Submissions')
                        ->boolean(),
                ]),
            Section::make('Fields')
                ->schema([
                    RichEditor::make('content')
                        ->json()
                        ->customBlocks(FormFieldBlockRegistry::get())
                        ->toolbarButtons([])
                        ->fileAttachmentsDisk('s3-public')
                        ->placeholder('Drag blocks here to build your form')
                        ->hiddenLabel()
                        ->dehydrated(false)
                        ->columnSpanFull()
                        ->extraInputAttributes([
                            'style' => 'min-height: 12rem;',
                            'data-mapped-block-types' => implode(',', FormFieldBlockRegistry::getMappedBlockTypes()),
                        ]),
                ])
                ->hidden(fn (Application $record) => $record->is_wizard)
                ->disabled(),
            Repeater::make('steps')
                ->schema([
                    TextInput::make('label')
                        ->label('Step Title')
                        ->required()
                        ->string()
                        ->maxLength(255)
                        ->autocomplete(false)
                        ->columnSpanFull()
                        ->lazy(),
                    Textarea::make('description')
                        ->label('Step Description')
                        ->string()
                        ->columnSpanFull(),
                    RichEditor::make('content')
                        ->json()
                        ->customBlocks(FormFieldBlockRegistry::get())
                        ->toolbarButtons([])
                        ->fileAttachmentsDisk('s3-public')
                        ->placeholder('Drag blocks here to build your form')
                        ->hiddenLabel()
                        ->dehydrated(false)
                        ->columnSpanFull()
                        ->extraInputAttributes([
                            'style' => 'min-height: 12rem;',
                            'data-mapped-block-types' => implode(',', FormFieldBlockRegistry::getMappedBlockTypes()),
                        ]),
                ])
                ->addActionLabel('New step')
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->visible(fn (Application $record) => $record->is_wizard)
                ->disabled()
                ->relationship()
                ->orderColumn('sort')
                ->columnSpanFull(),
            Section::make('Appearance')
                ->schema([
                    TextEntry::make('title_font_weight'),
                    ColorEntry::make('title_color')
                        ->state(fn (Application $record): ?string => $record->title_color ? ColorEnum::tryFrom($record->title_color)?->getRgb() : null),
                    ColorEntry::make('primary_color')
                        ->state(fn (Application $record): ?string => $record->primary_color ? Color::convertToRgb(Color::all()[$record->primary_color][600]) : null),
                    TextEntry::make('rounding'),
                ])
                ->columns(),
        ];
    }
}
