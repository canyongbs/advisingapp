<?php

namespace AdvisingApp\Form\Filament\Resources\Forms\Schemas;

use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Form\Models\Form;
use CanyonGBS\Common\Enums\Color as ColorEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;

class FormInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->disabled()
            ->components([
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
                            ->visible(fn (Form $record) => $record->embed_enabled)
                            ->badge(),
                        IconEntry::make('is_authenticated')
                            ->label('Is Authenticated')
                            ->boolean(),
                        IconEntry::make('allow_view_past_submissions')
                            ->label('Allow View Past Submissions')
                            ->boolean(),
                        IconEntry::make('generate_prospects')
                            ->label('Generate Prospects')
                            ->boolean(),
                        IconEntry::make('is_wizard')
                            ->label('Multi-step form')
                            ->boolean(),
                        IconEntry::make('recaptcha_enabled')
                            ->label('Enable reCAPTCHA')
                            ->boolean(),
                    ]),
                Section::make('Fields')
                    ->schema([
                        static::fieldsViewer(),
                    ])
                    ->hidden(fn (Form $record) => $record->is_wizard),
                Repeater::make('steps')
                    ->schema([
                        TextInput::make('label')
                            ->label('Step Title')
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->label('Step Description')
                            ->columnSpanFull(),
                        static::fieldsViewer(),
                    ])
                    ->addActionLabel('New step')
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->visible(fn (Form $record) => $record->is_wizard)
                    ->relationship()
                    ->orderColumn('sort')
                    ->columnSpanFull(),
                Section::make('Appearance')
                    ->schema([
                        TextEntry::make('title_font_weight'),
                        ColorEntry::make('title_color')
                            ->state(fn (Form $record): ?string => $record->title_color ? ColorEnum::tryFrom($record->title_color)?->getRgb() : null),
                        ColorEntry::make('primary_color')
                            ->state(fn (Form $record): ?string => $record->primary_color ? Color::convertToRgb(Color::all()[$record->primary_color][600]) : null),
                        TextEntry::make('rounding'),
                    ])
                    ->columns(),
            ]);
    }

    protected static function fieldsViewer(): RichEditor
    {
        return RichEditor::make('content')
            ->json()
            ->customBlocks(FormFieldBlockRegistry::get())
            ->toolbarButtons([])
            ->placeholder('Drag blocks here to build your form')
            ->hiddenLabel()
            ->dehydrated(false)
            ->columnSpanFull()
            ->extraInputAttributes([
                'style' => 'min-height: 12rem;',
                'data-mapped-block-types' => implode(',', FormFieldBlockRegistry::getMappedBlockTypes()),
            ]);
    }
}
