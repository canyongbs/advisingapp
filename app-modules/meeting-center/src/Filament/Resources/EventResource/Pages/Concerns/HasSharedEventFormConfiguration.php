<?php

namespace AdvisingApp\MeetingCenter\Filament\Resources\EventResource\Pages\Concerns;

use App\Models\User;
use Filament\Forms\Get;
use Filament\Forms\Components\Grid;
use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\Form\Rules\IsDomain;
use App\Forms\Components\ColorSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use AdvisingApp\MeetingCenter\Models\Event;
use FilamentTiptapEditor\Enums\TiptapOutput;
use Filament\Forms\Components\DateTimePicker;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormStep;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormField;
use Filament\Forms\Components\Contracts\CanEntangleWithSingularRelationships;

trait HasSharedEventFormConfiguration
{
    public function fields(): array
    {
        /** @var User $user */
        $user = auth()->user();

        return [
            TextInput::make('title')
                ->string()
                ->required(),
            Textarea::make('description')
                ->string()
                ->nullable(),
            TextInput::make('location')
                ->string()
                ->nullable(),
            TextInput::make('capacity')
                ->integer()
                ->minValue(1)
                ->nullable(),
            DateTimePicker::make('starts_at')
                ->timezone($user->timezone)
                ->required(),
            DateTimePicker::make('ends_at')
                ->timezone($user->timezone)
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
                        ->disabled(fn (?EventRegistrationForm $record) => $record?->submissions()->exists()),
                    Section::make('Fields')
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

    public function fieldBuilder(): TiptapEditor
    {
        return TiptapEditor::make('content')
            ->output(TiptapOutput::Json)
            ->blocks(FormFieldBlockRegistry::get())
            ->tools(['bold', 'italic', 'small', '|', 'heading', 'bullet-list', 'ordered-list', 'hr', '|', 'link', 'grid', 'blocks'])
            ->placeholder('Drag blocks here to build your form')
            ->hiddenLabel()
            ->saveRelationshipsUsing(function (TiptapEditor $component, EventRegistrationForm | EventRegistrationFormStep $record) {
                if ($component->isDisabled()) {
                    return;
                }

                $form = $record instanceof EventRegistrationForm ? $record : $record->submissible;
                $formStep = $record instanceof EventRegistrationFormStep ? $record : null;

                EventRegistrationFormField::query()
                    ->whereBelongsTo($form, 'submissible')
                    ->when($formStep, fn (EloquentBuilder $query) => $query->whereBelongsTo($formStep, 'step'))
                    ->delete();

                $content = $component->decodeBlocksBeforeSave($component->getJSON(decoded: true));
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
