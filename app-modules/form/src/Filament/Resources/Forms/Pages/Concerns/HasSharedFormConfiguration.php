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

namespace AdvisingApp\Form\Filament\Resources\Forms\Pages\Concerns;

use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Form\Models\Form;
use AdvisingApp\Form\Models\FormField;
use AdvisingApp\Form\Models\FormStep;
use AdvisingApp\Form\Rules\IsDomain;
use AdvisingApp\IntegrationGoogleRecaptcha\Settings\GoogleRecaptchaSettings;
use App\Enums\FontWeight;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Closure;
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
                ->helperText('The name of this form will only display for form administrators.'),
            TextInput::make('title')
                ->string()
                ->maxLength(255)
                ->autocomplete(false)
                ->columnSpanFull()
                ->helperText('The title of this form will be displayed when the form is embedded.'),
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
            Toggle::make('is_authenticated')
                ->label('Requires authentication')
                ->helperText('If enabled, students and prospects must verify their email address before they can open and submit this form.')
                ->default((bool) request()->query('is_authenticated'))
                ->disabled()
                ->dehydrated(),
            Toggle::make('generate_prospects')
                ->label('Generate Prospects')
                ->helperText('If enabled, the system will check the primary email address submitted on the form and create a prospect if no match is found.')
                ->default((bool) request()->query('generate_prospects'))
                ->disabled()
                ->dehydrated(),
            Toggle::make('is_wizard')
                ->label('Multi-step form')
                ->live()
                ->disabled(fn (?Form $record) => $record?->submissions()->submitted()->exists())
                ->columnStart(1),
            Toggle::make('recaptcha_enabled')
                ->label('Enable reCAPTCHA')
                ->live()
                ->disabled(fn (GoogleRecaptchaSettings $settings) => ! $settings->is_enabled)
                ->helperText(function (GoogleRecaptchaSettings $settings) {
                    if (! $settings->is_enabled) {
                        return 'Enable and configure reCAPTCHA in order to use it on your forms.';
                    }
                }),
            Section::make('Fields')
                ->schema([
                    $this->fieldBuilder()
                        ->rules([
                            function (Get $get): Closure {
                                return function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                                    $isAuthenticated = $get('is_authenticated');
                                    $generateProspects = $get('generate_prospects');

                                    if (! $generateProspects || $isAuthenticated) {
                                        return;
                                    }

                                    $this->validateNormalFormFromRules($fail);
                                };
                            },
                        ]),
                ])
                ->hidden(fn (Get $get) => $get('is_wizard'))
                ->disabled(fn (?Form $record) => $record?->submissions()->submitted()->exists()),
            Repeater::make('steps')
                ->schema([
                    TextInput::make('label')
                        ->required()
                        ->string()
                        ->maxLength(255)
                        ->autocomplete(false)
                        ->columnSpanFull()
                        ->lazy(),
                    $this->fieldBuilder(
                        isAuthenticatedPath: '../../is_authenticated',
                        generateProspectsPath: '../../generate_prospects'
                    ),
                ])
                ->addActionLabel('New step')
                ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                ->visible(fn (Get $get) => $get('is_wizard'))
                ->disabled(fn (?Form $record) => $record?->submissions()->submitted()->exists())
                ->relationship()
                ->reorderable()
                ->columnSpanFull()
                ->validationAttribute('steps')
                ->rules([
                    function (Get $get): Closure {
                        return function (string $attribute, mixed $value, Closure $fail) use ($get): void {
                            $isAuthenticated = $get('is_authenticated');
                            $generateProspects = $get('generate_prospects');

                            if (! $generateProspects || $isAuthenticated) {
                                return;
                            }

                            $this->validateWizardStepsFromRules($value, $fail);
                        };
                    },
                ]),
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

    public function fieldBuilder(string $isAuthenticatedPath = 'is_authenticated', string $generateProspectsPath = 'generate_prospects'): RichEditor
    {
        return RichEditor::make('content')
            ->json()
            ->customBlocks(fn (Get $get): array => FormFieldBlockRegistry::get(
                ($get($isAuthenticatedPath) ?? false) || ($get($generateProspectsPath) ?? false)
            ))
            ->toolbarButtons([
                ['bold', 'italic', 'link'],
                [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                ['small'],
                ['grid', 'customBlocks'],
            ])
            ->activePanel('customBlocks')
            ->placeholder('Drag blocks here to build your form')
            ->hiddenLabel()
            ->saveRelationshipsUsing(function (RichEditor $component, Form | FormStep $record) {
                if ($component->isDisabled()) {
                    return;
                }

                $form = $record instanceof Form ? $record : $record->submissible;
                $formStep = $record instanceof FormStep ? $record : null;

                FormField::query()
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

            /** @var FormField $field */
            $field = $form->fields()->findOrNew($id);
            $field->step()->associate($formStep);
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
        /** @var Form $record */
        $record = $this->record;

        if ($record->is_wizard) {
            $record->content = null;
            $record->save();

            return;
        }

        $record->steps()->delete();
    }
}
