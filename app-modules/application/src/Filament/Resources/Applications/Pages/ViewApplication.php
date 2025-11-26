<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Application\Filament\Resources\Applications\Pages;

use AdvisingApp\Application\Filament\Resources\Applications\ApplicationResource;
use AdvisingApp\Application\Models\Application;
use AdvisingApp\Form\Actions\GenerateSubmissibleEmbedCode;
use AdvisingApp\Form\Filament\Blocks\FormFieldBlockRegistry;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use FilamentTiptapEditor\TiptapEditor;

class ViewApplication extends ViewRecord
{
    protected static string $resource = ApplicationResource::class;

    /**
     * @return Schema
     */
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
                            ->visible(fn (Application $record) => $record->embed_enabled)
                            ->badge(),
                        IconEntry::make('should_generate_prospects')
                            ->label('Generate Prospects')
                            ->boolean(),
                        IconEntry::make('is_wizard')
                            ->label('Multi-step form')
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
                            ->default(
                                fn (Application $record) => ! is_null($record->content)
                                        ? tiptap_converter()
                                            ->record($record, 'content')
                                            ->asHTML($record->content)
                                        : null
                            )
                            ->extraInputAttributes(['style' => 'min-height: 12rem;']),
                    ])
                    ->hidden(fn (Application $record) => $record->is_wizard)
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
                        TiptapEditor::make('content')
                            ->blocks(FormFieldBlockRegistry::get())
                            ->tools(['bold', 'italic', 'small', '|', 'heading', 'bullet-list', 'ordered-list', 'hr', '|', 'link', 'grid', 'blocks'])
                            ->placeholder('Drag blocks here to build your form')
                            ->hiddenLabel()
                            ->default(
                                fn (Application $record) => ! is_null($record->content)
                                        ? tiptap_converter()
                                            ->record($record, 'content')
                                            ->asHTML($record->content)
                                        : null
                            )
                            ->dehydrated(false)
                            ->columnSpanFull()
                            ->extraInputAttributes(['style' => 'min-height: 12rem;']),
                    ])
                    ->addActionLabel('New step')
                    ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                    ->visible(fn (Application $record) => $record->is_wizard)
                    ->disabled()
                    ->relationship()
                    ->reorderable()
                    ->columnSpanFull(),
                Section::make('Appearance')
                    ->schema([
                        ColorEntry::make('primary_color')
                            ->state(fn (Application $record): ?string => $record->primary_color ? Color::convertToRgb(Color::all()[$record->primary_color][600]) : null),
                        TextEntry::make('rounding'),
                    ])
                    ->columns(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview')
                ->icon('heroicon-o-eye')
                ->url(fn (Application $application) => route('applications.preview', $application))
                ->openUrlInNewTab(),
            Action::make('view')
                ->url(fn (Application $application) => route('applications.show', ['application' => $application]))
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->openUrlInNewTab(),
            Action::make('embed_snippet')
                ->label('Embed Snippet')
                ->schema(
                    [
                        TextEntry::make('snippet')
                            ->label('Click to Copy')
                            ->state(function (Application $application) {
                                $code = resolve(GenerateSubmissibleEmbedCode::class)->handle($application);

                                $state = <<<EOD
                                ```
                                {$code}
                                ```
                                EOD;

                                return str($state)->markdown()->toHtmlString();
                            })
                            ->copyable()
                            ->copyableState(fn (Application $application) => resolve(GenerateSubmissibleEmbedCode::class)->handle($application))
                            ->copyMessage('Copied!')
                            ->copyMessageDuration(1500)
                            ->extraAttributes(['class' => 'embed-code-snippet']),
                    ]
                )
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Close')
                ->hidden(fn (Application $application) => ! $application->embed_enabled),
            DeleteAction::make(),
        ];
    }
}
