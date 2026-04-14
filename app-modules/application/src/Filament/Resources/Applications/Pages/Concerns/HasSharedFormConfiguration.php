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

namespace AdvisingApp\Application\Filament\Resources\Applications\Pages\Concerns;

use AdvisingApp\Application\Models\Application;
use AdvisingApp\Application\Models\ApplicationField;
use AdvisingApp\Application\Models\ApplicationStep;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Form\Rules\IsDomain;
use App\Enums\FontWeight;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait HasSharedFormConfiguration
{
    public function fields(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->string()
                ->maxLength(255)
                ->unique(ignoreRecord: true)
                ->autocomplete(false)
                ->columnSpanFull()
                ->helperText('The name of this application will only display for form administrators.'),
            TextInput::make('title')
                ->string()
                ->maxLength(255)
                ->autocomplete(false)
                ->columnSpanFull()
                ->helperText('The title of this application will be displayed when the form is embedded.'),
            Textarea::make('description')
                ->string()
                ->columnSpanFull(),
            Grid::make()
                ->schema([
                    Toggle::make('embed_enabled')
                        ->label('Embed Enabled')
                        ->live()
                        ->helperText('If enabled, this form can be embedded on other websites.'),
                    TagsInput::make('allowed_domains')
                        ->label('Allowed Domains')
                        ->helperText('Only these domains will be allowed to embed this form.')
                        ->placeholder('example.com')
                        ->hidden(fn (Get $get) => ! $get('embed_enabled'))
                        ->disabled(fn (Get $get) => ! $get('embed_enabled'))
                        ->nestedRecursiveRules(
                            [
                                'string',
                                new IsDomain(),
                            ]
                        ),
                ])
                ->columnSpanFull(),
            Toggle::make('is_wizard')
                ->label('Multi-step form')
                ->live()
                ->disabled(fn (?Application $record) => $record?->submissions()->exists()),
            Toggle::make('should_generate_prospects')
                ->label('Generate Prospects')
                ->helperText('If enabled, a request to submit by an unknown prospect will result in a new prospect being created.')
                ->disabled(fn () => ! auth()->user()?->hasLicense(LicenseType::RecruitmentCrm))
                ->hintIcon(fn () => ! auth()->user()?->hasLicense(LicenseType::RecruitmentCrm) ? 'heroicon-m-lock-closed' : null),
            Section::make('Fields')
                ->schema([
                    $this->fieldBuilder(),
                ])
                ->hidden(fn (Get $get) => $get('is_wizard'))
                ->disabled(fn (?Application $record) => $record?->submissions()->exists()),
            Repeater::make('steps')
                ->schema([
                    TextInput::make('label')
                        ->required()
                        ->string()
                        ->maxLength(255)
                        ->autocomplete(false)
                        ->columnSpanFull()
                        ->lazy(),
                    $this->fieldBuilder(),
                ])
                ->addActionLabel('New step')
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->visible(fn (Get $get) => $get('is_wizard'))
                ->disabled(fn (?Application $record) => $record?->submissions()->exists())
                ->relationship()
                ->reorderable()
                ->columnSpanFull(),
            Section::make('Appearance')
                ->schema([
                    Select::make('title_font_weight')
                        ->options(FontWeight::class),
                    ColorSelect::make('title_color'),
                    ColorSelect::make('primary_color'),
                    Select::make('rounding')
                        ->options(Rounding::class),
                ])
                ->columns(),
        ];
    }

    public function fieldBuilder(): RichEditor
    {
        return RichEditor::make('content')
            ->json()
            ->customBlocks(FormFieldBlockRegistry::get())
            ->toolbarButtons([
                ['bold', 'italic', 'link'],
                [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                ['small'],
                ['attachFiles'],
                ['grid', 'customBlocks'],
            ])
            ->resizableImages()
            ->fileAttachmentsDisk('s3-public')
            ->activePanel('customBlocks')
            ->placeholder('Drag blocks here to build your form')
            ->hiddenLabel()
            ->saveRelationshipsUsing(function (RichEditor $component, Application | ApplicationStep $record) {
                if ($component->isDisabled()) {
                    return;
                }

                $application = $record instanceof Application ? $record : $record->submissible;
                $applicationStep = $record instanceof ApplicationStep ? $record : null;

                ApplicationField::query()
                    ->whereBelongsTo($application, 'submissible')
                    ->when($applicationStep, fn (EloquentBuilder $query) => $query->whereBelongsTo($applicationStep, 'step'))
                    ->delete();

                $content = $component->getState();

                if (is_string($content)) {
                    $content = json_decode($content, true);
                }

                if (! is_array($content)) {
                    $content = [];
                }

                $content['content'] = $this->saveFieldsFromComponents(
                    $application,
                    $content['content'] ?? [],
                    $applicationStep,
                );

                $record->content = $content;
                $record->save();

                $component->state($content);

                $component->saveFileAttachmentsToRecord();
            })
            ->dehydrated(false)
            ->columnSpanFull()
            ->extraInputAttributes(['style' => 'min-height: 12rem;']);
    }

    public function saveFieldsFromComponents(Application $application, array $components, ?ApplicationStep $applicationStep): array
    {
        foreach ($components as $componentKey => $component) {
            if (array_key_exists('content', $component)) {
                $components[$componentKey]['content'] = $this->saveFieldsFromComponents($application, $component['content'], $applicationStep);

                continue;
            }

            if (($component['type'] ?? null) !== 'customBlock') {
                continue;
            }

            $componentAttributes = $component['attrs'] ?? [];
            $config = $componentAttributes['config'] ?? [];

            $id = $config['fieldId'] ?? null;
            unset($config['fieldId']);

            $label = $config['label'] ?? null;
            unset($config['label']);

            $isRequired = $config['isRequired'] ?? null;
            unset($config['isRequired']);

            /** @var ApplicationField $field */
            $field = $application->fields()->findOrNew($id);
            $field->step()->associate($applicationStep);
            $field->label = $label ?? $componentAttributes['id'];
            $field->is_required = $isRequired ?? false;
            $field->type = $componentAttributes['id'];
            $field->config = $config;
            $field->save();

            $components[$componentKey]['attrs']['config']['fieldId'] = $field->id;
        }

        return $components;
    }

    protected function afterCreate(): void
    {
        $this->clearFormContentForWizard();
    }

    protected function afterSave(): void
    {
        $this->clearFormContentForWizard();
    }

    protected function clearFormContentForWizard(): void
    {
        /** @var Application $record */
        $record = $this->record;

        if ($record->is_wizard) {
            $record->content = null;
            $record->save();

            return;
        }

        $record->steps()->delete();
    }
}
