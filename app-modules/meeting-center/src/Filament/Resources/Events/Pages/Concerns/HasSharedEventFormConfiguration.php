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

namespace AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages\Concerns;

use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Form\Rules\IsDomain;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use App\Features\EventVersioningFeature;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

trait HasSharedEventFormConfiguration
{
    public function fields(): array
    {
        return [
            TextInput::make('title')
                ->string()
                ->required(),
            TextInput::make('location')
                ->string()
                ->nullable(),
            TextInput::make('capacity')
                ->integer()
                ->minValue(1)
                ->nullable(),
            DateTimePicker::make('starts_at')
                ->seconds(false)
                ->required(),
            DateTimePicker::make('ends_at')
                ->seconds(false)
                ->required(),
            Fieldset::make('Registration Form')
                ->relationship('eventRegistrationForm')
                ->saveRelationshipsBeforeChildrenUsing(static function (Component | CanEntangleWithSingularRelationships $component): void {
                    $component->getCachedExistingRecord()?->delete();

                    $relationship = $component->getRelationship();

                    $data = $component->getChildComponentContainer()->getState(shouldCallHooksBefore: false);
                    $data = $component->mutateRelationshipDataBeforeCreate($data);

                    $relatedModel = $component->getRelatedModel();

                    $record = new $relatedModel();
                    $record->fill($data);

                    $relationship->save($record);

                    $component->cachedExistingRecord($record);
                })
                ->saveRelationshipsUsing(null)
                ->schema([
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
                        ->disabled(fn (?EventRegistrationForm $record) => ! EventVersioningFeature::active() && $record?->submissions()->exists()),
                    Section::make('Fields')
                        ->schema([
                            $this->fieldBuilder(),
                        ])
                        ->hidden(fn (Get $get) => $get('is_wizard'))
                        ->disabled(fn (?EventRegistrationForm $record) => ! EventVersioningFeature::active() && $record?->submissions()->exists()),
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
                        ->disabled(fn (?EventRegistrationForm $record) => ! EventVersioningFeature::active() && $record?->submissions()->exists())
                        ->relationship()
                        ->orderColumn('sort')
                        ->columnSpanFull(),
                    Section::make('Appearance')
                        ->schema([
                            ColorSelect::make('primary_color'),
                            Select::make('rounding')
                                ->options(Rounding::class),
                        ])
                        ->columns(),
                ]),
        ];
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
                if ($component->isDisabled()) {
                    return;
                }

                $form = $record instanceof EventRegistrationForm ? $record : $record->submissible;
                $formStep = $record instanceof EventRegistrationFormStep ? $record : null;

                EventRegistrationFormField::query()
                    ->whereBelongsTo($form, 'submissible')
                    ->when($formStep, fn (EloquentBuilder $query) => $query->whereBelongsTo($formStep, 'step'))
                    ->delete();

                $content = $component->getState();

                if (is_string($content)) {
                    $content = json_decode($content, true);
                }

                if (! is_array($content)) {
                    $content = [];
                }

                $content['content'] = $this->saveFieldsFromComponents(
                    $form,
                    $content['content'] ?? [],
                    $formStep,
                );

                $record->content = $content;
                $record->save();

                $component->state($content);
            })
            ->dehydrated(false)
            ->columnSpanFull()
            ->extraInputAttributes(['style' => 'min-height: 12rem;']);
    }

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
            $config = $componentAttributes['config'] ?? [];

            $id = $config['fieldId'] ?? null;
            unset($config['fieldId']);

            $label = $config['label'] ?? null;
            unset($config['label']);

            $isRequired = $config['isRequired'] ?? null;
            unset($config['isRequired']);

            /** @var EventRegistrationFormField $field */
            $field = $form->fields()->findOrNew($id);
            $field->step()->associate($eventRegistrationFormStep);
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
        /** @var Event $event */
        $event = $this->record;

        $form = $event->eventRegistrationForm;

        if ($form?->is_wizard) {
            $form->content = null;
            $form->save();

            return;
        }

        $form?->steps()->delete();
    }
}
