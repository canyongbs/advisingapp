<?php

namespace AdvisingApp\Form\Filament\Resources\Forms\Pages;

use AdvisingApp\Application\Filament\Resources\Applications\Pages\Concerns\HasSharedFormConfiguration;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use AdvisingApp\Form\Filament\Resources\Forms\FormResource;
use AdvisingApp\Form\Models\Form;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use FilamentTiptapEditor\TiptapEditor;

class ViewForm extends ViewRecord
{
    use HasSharedFormConfiguration;

    protected static string $resource = FormResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema
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
                        TiptapEditor::make('content')
                            ->blocks(FormFieldBlockRegistry::get())
                            ->tools(['bold', 'italic', 'small', '|', 'heading', 'bullet-list', 'ordered-list', 'hr', '|', 'link', 'grid', 'blocks', 'media'])
                            ->placeholder('Drag blocks here to build your form')
                            ->hiddenLabel()
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->extraInputAttributes(['style' => 'min-height: 12rem;']),
                    ])
                    ->hidden(fn (Form $record) => $record->is_wizard)
                    ->disabled(),
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
                    ->visible(fn (Form $record) => $record->is_wizard)
                    ->disabled()
                    ->relationship()
                    ->reorderable()
                    ->columnSpanFull(),
                Section::make('Appearance')
                    ->schema([
                        ColorEntry::make('primary_color'),
                        TextEntry::make('rounding'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
