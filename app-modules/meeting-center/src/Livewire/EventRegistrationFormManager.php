<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Livewire;

use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\MeetingCenter\Actions\CreateEventRegistrationFormVersion;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use App\Features\StepDescriptionFeature;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Support\Facades\DB;

class EventRegistrationFormManager extends EventFormManager
{
    protected bool $registrationFormVersionedInCurrentSave = false;

    /** @var array<array-key, EventRegistrationFormStep> */
    protected array $wizardStepVersionMap = [];

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->record)
            ->statePath('data')
            ->components([
                Fieldset::make('Registration Form')
                    ->relationship('eventRegistrationForm')
                    ->saveRelationshipsBeforeChildrenUsing(function (Component | CanEntangleWithSingularRelationships $component): void {
                        $relationship = $component->getRelationship();
                        $record = $component->getCachedExistingRecord();
                        $data = $component->getChildComponentContainer()->getState(shouldCallHooksBefore: false);

                        if ($record instanceof EventRegistrationForm) {
                            DB::transaction(function () use ($component, $record, $data): void {
                                $newVersion = app(CreateEventRegistrationFormVersion::class)->execute($record, $data);

                                if ($newVersion->is_wizard) {
                                    $sort = 1;
                                    $wizardStepVersionMap = [];

                                    $repeaterState = collect($component->getChildComponentContainer()->getComponents(withHidden: true, withActions: false))
                                        ->first(fn($component) => $component instanceof Repeater && $component->getName() === 'steps')
                                        ?->getRawState();

                                    $steps = ! empty($repeaterState)
                                        ? $repeaterState
                                        : $record->steps()->orderBy('sort')->get()
                                        ->mapWithKeys(fn(EventRegistrationFormStep $step) => [$step->id => ['label' => $step->label, 'description' => $step->description]])
                                        ->all();

                                    foreach ($steps as $key => $stepData) {
                                        $newStep = $newVersion->steps()->create([
                                            'label' => $stepData['label'] ?? 'Untitled Step',
                                            ...(StepDescriptionFeature::active() ? ['description' => $stepData['description'] ?? null] : []),
                                            'sort' => $sort++,
                                        ]);

                                        $mapKey = str_starts_with((string) $key, 'record-') ? substr((string) $key, 7) : (string) $key;
                                        $wizardStepVersionMap[$mapKey] = $newStep;

                                        if (! str_starts_with((string) $key, 'record-')) {
                                            $stepContent = $stepData['content'] ?? null;

                                            if (is_string($stepContent)) {
                                                $stepContent = json_decode($stepContent, true);
                                            }

                                            if (is_array($stepContent) && ! empty($stepContent)) {
                                                $stepContent['content'] = $this->saveFieldsFromComponents($newVersion, $stepContent['content'] ?? [], $newStep);
                                                $newStep->content = $stepContent;
                                                $newStep->save();
                                            }
                                        }
                                    }

                                    $this->wizardStepVersionMap = $wizardStepVersionMap;
                                }

                                $component->cachedExistingRecord($newVersion);
                            });

                            $this->registrationFormVersionedInCurrentSave = ! empty($this->wizardStepVersionMap);
                        } else {
                            $data = $component->mutateRelationshipDataBeforeCreate($data);

                            $relatedModel = $component->getRelatedModel();

                            $record = new $relatedModel();
                            $record->fill($data);

                            $relationship->save($record);

                            $component->cachedExistingRecord($record);
                        }
                    })
                    ->saveRelationshipsUsing(null)
                    ->schema([
                        Section::make('Options')
                            ->schema([
                                Toggle::make('is_wizard')
                                    ->label('Multi-step form')
                                    ->live()
                                    ->disabled(fn(?EventRegistrationForm $record) => $record?->submissions()->exists()),
                            ]),
                        Section::make('Form Fields')
                            ->schema([
                                $this->fieldBuilder(),
                            ])
                            ->hidden(fn(Get $get) => $get('is_wizard'))
                            ->disabled(fn(?EventRegistrationForm $record) => $record?->submissions()->exists()),
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
                                    ->columnSpanFull()
                                    ->visible(fn(): bool => StepDescriptionFeature::active()),
                                $this->fieldBuilder(),
                            ])
                            ->addActionLabel('New step')
                            ->itemLabel(fn(array $state): ?string => $state['label'] ?? null)
                            ->visible(fn(Get $get) => $get('is_wizard'))
                            ->disabled(fn(?EventRegistrationForm $record) => $record?->submissions()->exists())
                            ->relationship()
                            ->orderColumn('sort')
                            ->saveRelationshipsUsing(function (Repeater $component): void {
                                if ($this->registrationFormVersionedInCurrentSave) {
                                    return;
                                }

                                $component->saveToRelationship();
                            })
                            ->reorderable()
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public function fieldBuilder(): RichEditor
    {
        return RichEditor::make('content')
            ->json()
            ->customBlocks(FormFieldBlockRegistry::getForEvents())
            ->toolbarButtons([
                ['bold', 'italic', 'link'],
                [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                ['small'],
                ['grid', 'customBlocks'],
            ])
            ->activePanel('customBlocks')
            ->placeholder('Drag blocks here to build your form')
            ->hiddenLabel()
            ->saveRelationshipsUsing(function (RichEditor $component, EventRegistrationForm | EventRegistrationFormStep $record) {
                if ($this->registrationFormVersionedInCurrentSave) {
                    if ($record instanceof EventRegistrationFormStep) {
                        $newStep = $this->wizardStepVersionMap[(string) $record->id] ?? null;

                        if ($newStep !== null) {
                            if ($component->isDisabled()) {
                                return;
                            }

                            $form = $newStep->submissible;
                            assert($form instanceof EventRegistrationForm);

                            $this->saveFieldContentToRecord($component, $form, $newStep, $newStep, $record->content);
                        }
                    }

                    return;
                }

                if ($component->isDisabled()) {
                    return;
                }

                $form = $record instanceof EventRegistrationForm ? $record : $record->submissible;
                assert($form instanceof EventRegistrationForm);
                $formStep = $record instanceof EventRegistrationFormStep ? $record : null;

                $this->saveFieldContentToRecord($component, $form, $formStep, $record);
            })
            ->dehydrated(false)
            ->columnSpanFull()
            ->extraInputAttributes([
                'style' => 'min-height: 12rem;',
                'data-mapped-block-types' => implode(',', FormFieldBlockRegistry::getMappedBlockTypes()),
            ]);
    }

    /**
     * @param  array<int, array<string, mixed>>  $components
     *
     * @return array<int, array<string, mixed>>
     */
    public function saveFieldsFromComponents(EventRegistrationForm $form, array $components, ?EventRegistrationFormStep $eventRegistrationFormStep): array
    {
        foreach ($components as $componentKey => $component) {
            if (array_key_exists('content', $component)) {
                $components[$componentKey]['content'] = $this->saveFieldsFromComponents($form, $component['content'], $eventRegistrationFormStep);

                continue;
            }

            if (($component['type'] ?? null) !== 'customBlock') {
                continue;
            }

            $componentAttributes = $component['attrs'] ?? [];
            $blockId = $componentAttributes['id'] ?? null;

            // A block without an id has no known field type to save as.
            if (blank($blockId)) {
                continue;
            }

            $config = $componentAttributes['config'] ?? [];
            $id = $config['fieldId'] ?? null;
            unset($config['fieldId']);
            $label = $config['label'] ?? null;
            unset($config['label']);
            $isRequired = $config['isRequired'] ?? null;
            unset($config['isRequired']);

            $field = $form->fields()->findOrNew($id);
            assert($field instanceof EventRegistrationFormField);
            $field->step()->associate($eventRegistrationFormStep);
            $field->label = $label ?? $blockId;
            $field->is_required = $isRequired ?? false;
            $field->type = $blockId;
            $field->config = $config;
            $field->save();

            $components[$componentKey]['attrs']['config']['fieldId'] = $field->id;
        }

        return $components;
    }

    /**
     * @param  array<array-key, mixed>|null  $fallbackContent
     */
    protected function saveFieldContentToRecord(
        RichEditor $component,
        EventRegistrationForm $form,
        ?EventRegistrationFormStep $step,
        EventRegistrationForm|EventRegistrationFormStep $target,
        array|null $fallbackContent = null,
    ): void {
        EventRegistrationFormField::query()
            ->whereBelongsTo($form, 'submissible')
            ->when($step, fn(EloquentBuilder $query) => $query->whereBelongsTo($step, 'step'))
            ->delete();

        $content = $component->getState();

        if (is_string($content)) {
            $content = json_decode($content, true);
        }

        if (! is_array($content) || empty($content)) {
            $content = $fallbackContent ?? [];
        }

        $content['content'] = $this->saveFieldsFromComponents($form, $content['content'] ?? [], $step);
        $target->content = $content;
        $target->save();
        $component->state($content);
    }

    protected function attributesToSave(): array
    {
        return [];
    }

    protected function afterSave(): void
    {
        $form = $this->record->eventRegistrationForm;

        if ($form?->is_wizard) {
            $form->content = null;
            $form->save();
        } else {
            $form?->steps()->delete();
        }

        $this->registrationFormVersionedInCurrentSave = false;
        $this->wizardStepVersionMap = [];
    }
}
