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

use AdvisingApp\Form\Enums\Rounding;
use AdvisingApp\Form\Rules\IsDomain;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\EventResource;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use CanyonGBS\Common\Filament\Forms\Components\ColorSelect;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class EditEventDetails extends EditRecord
{
    use EditPageRedirection;

    protected static string $resource = EventResource::class;

    protected static ?string $navigationLabel = 'Details';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Event Details')
                ->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->string()
                        ->required()
                        ->maxLength(255),
                    TextInput::make('location')
                        ->label('Location')
                        ->string()
                        ->nullable(),
                    TextInput::make('capacity')
                        ->label('Capacity')
                        ->integer()
                        ->minValue(1)
                        ->nullable(),
                    DateTimePicker::make('starts_at')
                        ->label('Starts at')
                        ->seconds(false)
                        ->required(),
                    DateTimePicker::make('ends_at')
                        ->label('Ends at')
                        ->seconds(false)
                        ->required(),
                ])
                ->columns(2),

            Fieldset::make('Embed Settings')
                ->relationship('eventRegistrationForm')
                ->schema([
                    Toggle::make('embed_enabled')
                        ->label('Embed Enabled')
                        ->live()
                        ->helperText('If enabled, this event page can be embedded on other websites.'),
                    TagsInput::make('allowed_domains')
                        ->label('Allowed Domains')
                        ->helperText('Only these domains will be allowed to embed this event page.')
                        ->placeholder('example.com')
                        ->hidden(fn (Get $get): bool => ! $get('embed_enabled'))
                        ->disabled(fn (Get $get): bool => ! $get('embed_enabled'))
                        ->nestedRecursiveRules([
                            'string',
                            new IsDomain(),
                        ]),
                ])
                ->columns(1),

            Fieldset::make('Appearance Settings')
                ->relationship('eventRegistrationForm')
                ->schema([
                    ColorSelect::make('primary_color')
                        ->label('Primary Color'),
                    Select::make('rounding')
                        ->label('Rounding')
                        ->options(Rounding::class),
                ])
                ->columns(2),
        ]);
    }
}
