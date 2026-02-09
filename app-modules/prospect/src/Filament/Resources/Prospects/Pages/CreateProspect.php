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

namespace AdvisingApp\Prospect\Filament\Resources\Prospects\Pages;

use AdvisingApp\Prospect\Filament\Resources\Prospects\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\Size;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class CreateProspect extends CreateRecord
{
    protected static string $resource = ProspectResource::class;

    public function form(Schema $schema): Schema
    {
        $generateFullName = function (Get $get, Set $set) {
            $firstName = trim($get('first_name'));

            if (blank($firstName)) {
                return;
            }

            $lastName = trim($get('last_name'));

            if (blank($lastName)) {
                return;
            }

            $set(Prospect::displayNameKey(), "{$firstName} {$lastName}");
        };

        $makeRepeaterItemPrimaryAction = fn (): Action => Action::make('makePrimary')
            ->tooltip('Make primary')
            ->icon('heroicon-m-star')
            ->iconButton()
            ->size(Size::Small)
            ->color('primary')
            ->action(function (array $arguments, Repeater $component): void {
                $items = $component->getState();
                $primaryItem = $items[$arguments['item']];
                unset($items[$arguments['item']]);

                $component->state([
                    $arguments['item'] => $primaryItem,
                    ...$items,
                ]);
            })
            ->visible(fn (array $arguments, Repeater $component): bool => array_key_first($component->getState()) !== $arguments['item']);

        return $schema
            ->components([
                Section::make('Demographics')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated($generateFullName),
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated($generateFullName),
                        TextInput::make(Prospect::displayNameKey())
                            ->label('Full Name')
                            ->required()
                            ->disabled()
                            ->dehydrated()
                            ->maxLength(255),
                        TextInput::make('preferred')
                            ->label('Preferred Name')
                            ->maxLength(255),
                        // TODO: Display this based on system configurable data format
                        DatePicker::make('birthdate')
                            ->label('Birthdate')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->format('Y-m-d')
                            ->displayFormat('Y-m-d')
                            ->maxDate(now()),
                        TextInput::make('hsgrad')
                            ->label('High School Graduation Year')
                            ->nullable()
                            ->numeric()
                            ->minValue(1920)
                            ->maxValue(now()->addYears(25)->year),
                    ])
                    ->columns(2),
                Section::make('Contact Information')
                    ->schema([
                        Repeater::make('emailAddresses')
                            ->relationship()
                            ->schema([
                                TextInput::make(name: 'address')
                                    ->label('Address')
                                    ->email()
                                    ->required()
                                    ->placeholder('example@gmail.com')
                                    ->maxLength(255)
                                    ->columnSpan(['lg' => 2]),
                                TextInput::make('type')
                                    ->label('Type')
                                    ->maxLength(255)
                                    ->placeholder('Personal')
                                    ->default('Personal')
                                    ->datalist([
                                        'Personal',
                                        'Work',
                                        'Institutional',
                                    ]),
                            ])
                            ->orderColumn('order')
                            ->itemLabel(fn (Repeater $component, Schema $schema): ?string => (Arr::first($component->getChildComponentContainers())->getStatePath() === $schema->getStatePath()) ? 'Primary email address' : 'Additional email address')
                            ->extraItemActions([
                                $makeRepeaterItemPrimaryAction(),
                            ])
                            ->columns(3)
                            ->hiddenLabel()
                            ->addActionLabel('Add email address')
                            ->addActionAlignment(Alignment::Start),
                        Repeater::make('phoneNumbers')
                            ->relationship()
                            ->schema([
                                Grid::make(['default' => 3])
                                    ->schema([
                                        PhoneInput::make(name: 'number')
                                            ->label('Number')
                                            ->required()
                                            ->columnSpan(2),
                                        TextInput::make(name: 'ext')
                                            ->label('Extension')
                                            ->integer()
                                            ->maxLength(8),
                                    ])
                                    ->columnSpan(['lg' => 2]),
                                TextInput::make('type')
                                    ->label('Type')
                                    ->maxLength(255)
                                    ->placeholder('Mobile')
                                    ->default('Mobile')
                                    ->datalist([
                                        'Mobile',
                                        'Home',
                                        'Work',
                                    ]),
                                Checkbox::make('can_receive_sms')
                                    ->label('Can receive SMS messages')
                                    ->columnSpanFull()
                                    ->default(true),
                            ])
                            ->orderColumn('order')
                            ->itemLabel(fn (Repeater $component, Schema $schema): ?string => (Arr::first($component->getChildComponentContainers())->getStatePath() === $schema->getStatePath()) ? 'Primary phone number' : 'Additional phone number')
                            ->extraItemActions([
                                $makeRepeaterItemPrimaryAction(),
                            ])
                            ->columns(3)
                            ->hiddenLabel()
                            ->addActionLabel('Add phone number')
                            ->addActionAlignment(Alignment::Start),
                        Repeater::make('addresses')
                            ->relationship()
                            ->schema([
                                TextInput::make('line_1')
                                    ->label('Line 1')
                                    ->maxLength(255),
                                TextInput::make('line_2')
                                    ->label('Line 2')
                                    ->maxLength(255),
                                TextInput::make('line_3')
                                    ->label('Line 3')
                                    ->maxLength(255),
                                TextInput::make('city')
                                    ->label('City')
                                    ->maxLength(255),
                                TextInput::make('state')
                                    ->label('State')
                                    ->maxLength(255),
                                TextInput::make('postal')
                                    ->label('Postal code')
                                    ->maxLength(255),
                                TextInput::make('country')
                                    ->label('Country')
                                    ->maxLength(255),
                                TextInput::make('type')
                                    ->label('Type')
                                    ->maxLength(255)
                                    ->placeholder('Home')
                                    ->default('Home')
                                    ->datalist([
                                        'Home',
                                        'Institutional',
                                        'Work',
                                    ]),
                            ])
                            ->orderColumn('order')
                            ->itemLabel(fn (Repeater $component, Schema $schema): ?string => (Arr::first($component->getChildComponentContainers())->getStatePath() === $schema->getStatePath()) ? 'Primary address' : 'Additional address')
                            ->extraItemActions([
                                $makeRepeaterItemPrimaryAction(),
                            ])
                            ->columns(3)
                            ->hiddenLabel()
                            ->addActionLabel('Add address')
                            ->addActionAlignment(Alignment::Start),
                    ]),
                Section::make('Classification')
                    ->schema([
                        Select::make('status_id')
                            ->label('Status')
                            ->required()
                            ->relationship('status', 'name', fn (Builder $query) => $query->orderBy('sort'))
                            ->default(fn () => ProspectStatus::query()
                                ->orderBy('sort')
                                ->first()
                                ?->getKey())
                            ->exists(
                                table: (new ProspectStatus())->getTable(),
                                column: (new ProspectStatus())->getKeyName()
                            ),
                        Select::make('source_id')
                            ->label('Source')
                            ->required()
                            ->relationship('source', 'name')
                            ->default(fn () => ProspectSource::query()->orderBy('name')->first()?->getKey())
                            ->exists(
                                table: (new ProspectSource())->getTable(),
                                column: (new ProspectSource())->getKeyName()
                            ),
                        Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    protected function afterCreate(): void
    {
        /** Prospect $prospect */
        $prospect = $this->getRecord();

        $prospect->primaryEmailAddress()->associate($prospect->emailAddresses()->first());
        $prospect->primaryPhoneNumber()->associate($prospect->phoneNumbers()->first());
        $prospect->primaryAddress()->associate($prospect->addresses()->first());
        $prospect->save();
    }
}
