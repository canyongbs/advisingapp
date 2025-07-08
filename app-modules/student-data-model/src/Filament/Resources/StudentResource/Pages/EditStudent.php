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

namespace AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages;

use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\Concerns\HasStudentHeader;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Resources\Pages\EditRecord\Concerns\EditPageRedirection;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Arr;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class EditStudent extends EditRecord
{
    use EditPageRedirection;
    use HasStudentHeader;

    protected static string $resource = StudentResource::class;

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
                Section::make('Personal Information')
                    ->schema([
                        TextInput::make('sisid')
                            ->label('Student ID')
                            ->alphaDash()
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('otherid')
                            ->label('Other ID')
                            ->maxLength(255),
                        TextInput::make(Student::displayFirstNameKey())
                            ->label('First Name')
                            ->maxLength(255),
                        TextInput::make(Student::displayLastNameKey())
                            ->label('Last Name')
                            ->maxLength(255),
                        TextInput::make(Student::displayNameKey())
                            ->label('Full Name')
                            ->maxLength(255),
                        TextInput::make('preferred')
                            ->label('Preferred Name')
                            ->maxLength(255),
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
                            ->numeric(),
                        TextInput::make('gender')
                            ->nullable()
                            ->maxLength(255),
                    ])
                    ->columns(3),
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
                Section::make('Engagement Restrictions')
                    ->schema([
                        Select::make('sms_opt_out')
                            ->label('SMS Opt Out')
                            ->boolean(),
                        Select::make('email_bounce')
                            ->label('Email Bounce')
                            ->boolean(),
                        Select::make('dual')
                            ->label('Dual')
                            ->boolean(),
                        Select::make('ferpa')
                            ->label('FERPA')
                            ->boolean(),
                        Select::make('firstgen')
                            ->label('Firstgen')
                            ->boolean(),
                        Select::make('sap')
                            ->label('SAP')
                            ->boolean(),
                        TextInput::make('holds')
                            ->label('Holds')
                            ->maxLength(255),
                        DatePicker::make('dfw')
                            ->label('DFW')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->format('Y-m-d')
                            ->displayFormat('Y-m-d'),
                        TextInput::make('ethnicity')
                            ->label('Ethnicity')
                            ->maxLength(255),
                        DateTimePicker::make('lastlmslogin')
                            ->label('Last LMS login')
                            ->native(false)
                            ->closeOnDateSelection()
                            ->format('Y-m-d H:i:s')
                            ->displayFormat('Y-m-d H:i:s'),
                        TextInput::make('f_e_term')
                            ->label('First Enrollment Term')
                            ->maxLength(255),
                        TextInput::make('mr_e_term')
                            ->label('Most Recent Enrollment Term')
                            ->maxLength(255),
                    ])
                    ->columns(3),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        /** @var Student $student */
        $student = $this->getRecord();

        $student->primaryEmailAddress()->associate($student->emailAddresses()->first());
        $student->primaryPhoneNumber()->associate($student->phoneNumbers()->first());
        $student->primaryAddress()->associate($student->addresses()->first());
        $student->updated_at_source = now();
        $student->save();
    }
}
