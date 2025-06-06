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

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use AdvisingApp\Prospect\Concerns\ProspectHolisticViewPage;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Actions\ConvertToStudent;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Actions\DisassociateStudent;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\Concerns\HasProspectHeader;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class EditProspect extends EditRecord
{
    use ProspectHolisticViewPage;
    use EditPageRedirection;
    use HasProspectHeader;

    protected static string $resource = ProspectResource::class;

    // TODO: Automatically set from Filament
    protected static ?string $navigationLabel = 'Edit';

    public function form(Form $form): Form
    {
        $makeRepeaterItemPrimaryAction = fn (): Action => Action::make('makePrimary')
            ->tooltip('Make primary')
            ->icon('heroicon-m-star')
            ->iconButton()
            ->size(ActionSize::Small)
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

        return $form
            ->schema([
                Section::make('Demographics')
                    ->schema([
                        TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('last_name')
                            ->label('Last Name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make(Prospect::displayNameKey())
                            ->label('Full Name')
                            ->required()
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
                            ->itemLabel(fn (Repeater $component, ComponentContainer $container): ?string => (Arr::first($component->getChildComponentContainers())->getStatePath() === $container->getStatePath()) ? 'Primary email address' : 'Additional email address')
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
                            ->itemLabel(fn (Repeater $component, ComponentContainer $container): ?string => (Arr::first($component->getChildComponentContainers())->getStatePath() === $container->getStatePath()) ? 'Primary phone number' : 'Additional phone number')
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
                            ->itemLabel(fn (Repeater $component, ComponentContainer $container): ?string => (Arr::first($component->getChildComponentContainers())->getStatePath() === $container->getStatePath()) ? 'Primary address' : 'Additional address')
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
                            ->exists(
                                table: (new ProspectStatus())->getTable(),
                                column: (new ProspectStatus())->getKeyName()
                            ),
                        Select::make('source_id')
                            ->label('Source')
                            ->required()
                            ->relationship('source', 'name')
                            ->exists(
                                table: (new ProspectSource())->getTable(),
                                column: (new ProspectSource())->getKeyName()
                            ),
                        Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Engagement Restrictions')
                    ->schema([
                        Select::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->boolean(),
                        Select::make('email_bounce')
                            ->label('Email Bounce')
                            ->boolean(),
                    ])
                    ->columns(2),
                Section::make('Record Details')
                    ->schema([
                        Select::make('created_by_id')
                            ->label('Created By')
                            ->relationship('createdBy', 'name')
                            ->searchable()
                            ->nullable()
                            ->exists(
                                table: (new User())->getTable(),
                                column: (new User())->getKeyName()
                            ),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ConvertToStudent::make()->visible(fn (Prospect $record) => ! $record->student()->exists()),
            DisassociateStudent::make()->visible(fn (Prospect $record) => $record->student()->exists()),
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        /** Prospect $prospect */
        $prospect = $this->getRecord();

        $prospect->primaryEmailAddress()->associate($prospect->emailAddresses()->first());
        $prospect->primaryPhoneNumber()->associate($prospect->phoneNumbers()->first());
        $prospect->primaryAddress()->associate($prospect->addresses()->first());
        $prospect->save();
    }
}
