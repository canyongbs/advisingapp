<?php

namespace Assist\Form\Filament\Resources\FormResource\Pages\Concerns;

use Filament\Forms\Get;
use Assist\Form\Models\Form;
use Assist\Form\Rules\IsDomain;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Assist\Form\Filament\Blocks\SelectFormFieldBlock;
use Assist\Form\Filament\Blocks\TextAreaFormFieldBlock;
use Assist\Form\Filament\Blocks\TextInputFormFieldBlock;

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
            Section::make('Fields')
                ->schema([
                    Builder::make('fields')
                        ->label('')
                        ->columnSpanFull()
                        ->reorderableWithDragAndDrop(false)
                        ->reorderableWithButtons()
                        ->blocks([
                            TextInputFormFieldBlock::make(),
                            TextAreaFormFieldBlock::make(),
                            SelectFormFieldBlock::make(),
                        ]),
                ]),
        ];
    }

    public function handleFieldSaving(Form $form, array $fields): void
    {
        collect($fields)
            ->each(function ($field) use ($form) {
                $data = collect($field['data']);

                $form
                    ->fields()
                    ->create([
                        'key' => $data->get('key'),
                        'type' => $field['type'],
                        'label' => $data->get('label'),
                        'required' => $data->get('required'),
                        'config' => $data->except(['key', 'label', 'required']),
                    ]);
            });
    }
}
