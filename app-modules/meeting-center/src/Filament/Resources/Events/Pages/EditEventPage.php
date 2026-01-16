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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use AdvisingApp\Form\Rules\IsDomain;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use FilamentTiptapEditor\TiptapEditor;

class EditEventPage extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Event Page';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero Image')
                ->description('Upload a hero image for your event page')
                ->schema([
                    FileUpload::make('hero_image')
                        ->label('Hero Image')
                        ->image()
                        ->disk('public')
                        ->directory('event-images')
                        ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                        ->maxSize(5120) // 5MB
                        ->columnSpanFull(),
                ]),

            Section::make('Page Description')
                ->description('Create the content that will appear on your event page')
                ->schema([
                    TiptapEditor::make('description')
                        ->label('Description')
                        ->tools([
                            'bold',
                            'italic',
                            'strike',
                            '|',
                            'heading',
                            '|',
                            'bullet-list',
                            'ordered-list',
                            '|',
                            'link',
                            'blockquote',
                            'hr',
                        ])
                        ->placeholder('Enter your event description...')
                        ->columnSpanFull(),
                ]),

            Section::make('Registration Settings')
                ->description('Configure how registration is presented to users')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            Toggle::make('show_registration_popup')
                                ->label('Show Registration in Popup')
                                ->helperText('When enabled, registration will appear in a popup modal. When disabled, users will go to a full registration page.')
                                ->default(true),
                        ]),
                ]),

            Section::make('Embed Settings')
                ->description('Configure embedding options for this event page')
                ->schema([
                    Grid::make()
                        ->schema([
                            Toggle::make('embed_enabled')
                                ->label('Allow Event Page Embedding')
                                ->live()
                                ->helperText('If enabled, this event page can be embedded on other websites.'),
                            TagsInput::make('allowed_domains')
                                ->label('Allowed Domains')
                                ->helperText('Only these domains will be allowed to embed this event page.')
                                ->placeholder('example.com')
                                ->hidden(fn (Get $get) => ! $get('embed_enabled'))
                                ->disabled(fn (Get $get) => ! $get('embed_enabled'))
                                ->nestedRecursiveRules([
                                    'string',
                                    new IsDomain(),
                                ]),
                        ])
                        ->columnSpanFull(),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}