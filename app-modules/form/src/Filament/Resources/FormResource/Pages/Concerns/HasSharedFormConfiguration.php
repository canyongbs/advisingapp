<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Form\Filament\Resources\FormResource\Pages\Concerns;

use Filament\Forms\Get;
use Illuminate\Support\Arr;
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
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Assist\Form\Filament\Blocks\FormFieldBlockRegistry;

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
                ->hidden(fn (Get $get) => $get('is_wizard')),
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
                ->relationship()
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

    public function fieldBuilder(): Builder
    {
        return Builder::make('fields')
            ->hiddenLabel()
            ->columnSpanFull()
            ->reorderableWithDragAndDrop(false)
            ->reorderableWithButtons()
            ->blocks(FormFieldBlockRegistry::getInstances())
            ->addActionLabel('New field')
            ->dehydrated(false)
            ->loadStateFromRelationshipsUsing(function (Builder $component, Form | FormStep $record) {
                $fields = $record instanceof Form ?
                    $record->fields()->whereNull('step_id')->get() :
                    $record->fields;

                $component->state(
                    $fields
                        ->map(fn (FormField $field): array => [
                            'type' => $field->type,
                            'data' => [
                                'label' => $field->label,
                                'key' => $field->key,
                                'required' => $field->required,
                                ...$field->config,
                            ],
                        ])
                        ->all(),
                );
            })
            ->saveRelationshipsUsing(function (Get $get, Form | FormStep $record, array $state) {
                $record->fields()->delete();

                if ($record instanceof FormStep) {
                    $record->form->fields()->whereNull('step_id')->delete();
                } elseif ($record instanceof Form) {
                    $record->steps()->delete();
                }

                foreach ($state as $field) {
                    $fieldData = $field['data'];

                    $record
                        ->fields()
                        ->create([
                            'key' => $fieldData['key'],
                            'type' => $field['type'],
                            'label' => $fieldData['label'],
                            'required' => $fieldData['required'],
                            'config' => Arr::except($fieldData, ['key', 'label', 'required']),
                            ...($record instanceof FormStep ? ['form_id' => $record->form_id] : []),
                        ]);
                }
            });
    }
}
