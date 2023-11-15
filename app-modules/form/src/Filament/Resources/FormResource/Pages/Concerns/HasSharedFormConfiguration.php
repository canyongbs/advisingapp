<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages\Concerns;

use Filament\Forms\Get;
use Illuminate\Support\Str;
use Assist\Form\Models\Form;
use Assist\Form\Enums\Rounding;
use Assist\Form\Rules\IsDomain;
use Assist\Form\Models\FormStep;
use Assist\Form\Models\FormField;
use Filament\Support\Colors\Color;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use FilamentTiptapEditor\TiptapEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use FilamentTiptapEditor\Enums\TiptapOutput;
use Assist\Form\Filament\Blocks\FormFieldBlockRegistry;
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
                ->autocomplete(false)
                ->columnSpanFull(),
            Textarea::make('description')
                ->string()
                ->columnSpanFull(),
            Grid::make(2)
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
                ->columnSpanFull(),
            Section::make('Fields')
                ->schema([
                    $this->fieldBuilder(),
                ])
                ->hidden(fn (Get $get) => $get('is_wizard'))
                ->disabled(fn (?Form $record) => $record?->submissions()->exists()),
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
                ->disabled(fn (?Form $record) => $record?->submissions()->exists())
                ->relationship()
                ->reorderable()
                ->columnSpanFull(),
            Section::make('Appearance')
                ->schema([
                    Select::make('primary_color')
                        ->options(collect(Color::all())->keys()->mapWithKeys(fn (string $color): array => [
                            $color => Str::title($color),
                        ])->all()),
                    Select::make('rounding')
                        ->options(Rounding::class),
                ])
                ->columns(2),
        ];
    }

    public function fieldBuilder(): TiptapEditor
    {
        return TiptapEditor::make('content')
            ->output(TiptapOutput::Json)
            ->blocks(FormFieldBlockRegistry::get())
            ->tools(['heading', 'hr', 'bullet-list', 'ordered-list', '|', 'bold', 'italic', 'small', '|', 'link', 'grid', 'blocks'])
            ->hiddenLabel()
            ->saveRelationshipsUsing(function (TiptapEditor $component, Form | FormStep $record) {
                $form = $record instanceof Form ? $record : $record->form;
                $formStep = $record instanceof FormStep ? $record : null;

                FormField::query()
                    ->whereBelongsTo($form)
                    ->when($formStep, fn (EloquentBuilder $query) => $query->whereBelongsTo($formStep, 'step'))
                    ->delete();

                $content = $component->getJSON(decoded: true);
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

    public function saveFieldsFromComponents(Form $form, array $components, ?FormStep $formStep): array
    {
        foreach ($components as $componentKey => $component) {
            if (array_key_exists('content', $component)) {
                $components[$componentKey]['content'] = $this->saveFieldsFromComponents($form, $component['content'], $formStep);

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

            $field = $form->fields()->findOrNew($id ?? null);
            $field->step()->associate($formStep);
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
        if ($this->record->is_wizard) {
            $this->record->content = null;
            $this->record->save();

            return;
        }

        $this->record->steps()->delete();
    }
}
