<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.

</COPYRIGHT>
*/

namespace AdvisingApp\MeetingCenter\Livewire;

use AdvisingApp\MeetingCenter\Models\Event;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\RichEditor\ToolbarButtonGroup;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EventLandingPageManager extends EventFormManager
{
    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->record)
            ->statePath('data')
            ->components([
                Section::make('Landing Page')
                    ->description('Configure your event landing page with a hero image and description content.')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('hero_image')
                            ->label('Hero Image')
                            ->disk('s3-public')
                            ->collection('hero_image')
                            ->acceptedFileTypes(Event::HERO_IMAGE_MIME_TYPES)
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        RichEditor::make('description')
                            ->label('Description')
                            ->json()
                            ->fileAttachmentsDisk('s3-public')
                            ->fileAttachmentsVisibility('public')
                            ->resizableImages()
                            ->toolbarButtons([
                                ['bold', 'italic', 'link'],
                                [ToolbarButtonGroup::make('Heading', ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'])->textualButtons(), 'bulletList', 'orderedList', 'horizontalRule'],
                                ['textColor', 'small'],
                                ['attachFiles'],
                                ['clearFormatting'],
                                ['undo', 'redo'],
                            ])
                            ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function attributesToSave(): array
    {
        return ['description'];
    }
}
