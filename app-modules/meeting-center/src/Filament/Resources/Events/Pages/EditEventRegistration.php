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

namespace AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages;

use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\EventResource;
use AdvisingApp\MeetingCenter\Models\Event;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Contracts\CanEntangleWithSingularRelationships;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

class EditEventRegistration extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Registration Form';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Fieldset::make('Registration Form')
                ->relationship('eventRegistrationForm')
                ->saveRelationshipsBeforeChildrenUsing(static function (Component | CanEntangleWithSingularRelationships $component): void {
                    $relationship = $component->getRelationship();
                    $record = $component->getCachedExistingRecord();
                    $data = $component->getChildComponentContainer()->getState(shouldCallHooksBefore: false);

                    if ($record) {
                        $record->fill($data);
                        $record->save();
                    } else {
                        $data = $component->mutateRelationshipDataBeforeCreate($data);

                        $relatedModel = $component->getRelatedModel();

                        $record = new $relatedModel();
                        $record->fill($data);

                        $relationship->save($record);
                    }

                    $component->cachedExistingRecord($record);
                })
                ->saveRelationshipsUsing(null)
                ->schema([
                    Toggle::make('is_wizard')
                        ->label('Multi-step form')
                        ->live()
                        ->disabled(fn (?EventRegistrationForm $record) => $record?->submissions()->exists()),

                    Section::make('Form Fields')
                        ->schema([
                            $this->fieldBuilder(),
                        ])
                        ->hidden(fn (Get $get) => $get('is_wizard'))
                        ->disabled(fn (?EventRegistrationForm $record) => $record?->submissions()->exists()),

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
                        ->disabled(fn (?EventRegistrationForm $record) => $record?->submissions()->exists())
                        ->relationship()
                        ->reorderable()
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public function fieldBuilder(): TiptapEditor
    {
        return TiptapEditor::make('content')
            ->blocks(FormFieldBlockRegistry::getForEvents())
            ->tools(['bold', 'italic', 'small', '|', 'heading', 'bullet-list', 'ordered-list', 'hr', '|', 'link', 'grid', 'blocks'])
            ->placeholder('Drag blocks here to build your form')
            ->hiddenLabel()
            ->saveRelationshipsUsing(function (TiptapEditor $component, EventRegistrationForm | EventRegistrationFormStep $record) {
                if ($component->isDisabled()) {
                    return;
                }

                $record->wasRecentlyCreated && $component->processImages();

                $form = $record instanceof EventRegistrationForm ? $record : $record->submissible;
                assert($form instanceof EventRegistrationForm);
                $formStep = $record instanceof EventRegistrationFormStep ? $record : null;

                EventRegistrationFormField::query()
                    ->whereBelongsTo($form, 'submissible')
                    ->when($formStep, fn (EloquentBuilder $query) => $query->whereBelongsTo($formStep, 'step'))
                    ->delete();

                $content = [];

                if (filled($component->getState())) {
                    $content = $component->decodeBlocks($component->getJSON(decoded: true));
                }

                $content['content'] = $this->saveFieldsFromComponents(
                    $form,
                    $content['content'] ?? [],
                    $formStep,
                );

                $record->content = $content;
                $record->save();
            })
            ->dehydrated(false)
            ->columnSpanFull()
            ->extraInputAttributes(['style' => 'min-height: 12rem;']);
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

            if ($component['type'] !== 'tiptapBlock') {
                continue;
            }

            $componentAttributes = $component['attrs'] ?? [];

            if (array_key_exists('id', $componentAttributes)) {
                $id = $componentAttributes['id'] ?? null;
                unset($componentAttributes['id']);
            }

            if (array_key_exists('label', $componentAttributes['data'])) {
                $label = $componentAttributes['data']['label'] ?? null;
                unset($componentAttributes['data']['label']);
            }

            if (array_key_exists('isRequired', $componentAttributes['data'])) {
                $isRequired = $componentAttributes['data']['isRequired'] ?? null;
                unset($componentAttributes['data']['isRequired']);
            }

            /** @var EventRegistrationFormField $field */
            $field = $form->fields()->findOrNew($id ?? null);
            $field->step()->associate($eventRegistrationFormStep);
            $field->label = $label ?? $componentAttributes['type'];
            $field->is_required = $isRequired ?? false;
            $field->type = $componentAttributes['type'];
            $field->config = $componentAttributes['data'];
            $field->save();

            $components[$componentKey]['attrs']['id'] = $field->id;
        }

        return $components;
    }

    protected function afterSave(): void
    {
        $this->clearFormContentForWizard();
    }

    protected function clearFormContentForWizard(): void
    {
        $event = $this->getRecord();
        assert($event instanceof Event);
        $form = $event->eventRegistrationForm;

        if ($form?->is_wizard) {
            $form->content = null;
            $form->save();

            return;
        }

        $form?->steps()->delete();
    }
}
