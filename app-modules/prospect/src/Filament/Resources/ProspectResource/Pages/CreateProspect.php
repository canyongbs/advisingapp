<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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
    - Test

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages;

use App\Models\User;
use Filament\Forms\Form;
use App\Models\Scopes\HasLicense;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use AdvisingApp\Prospect\Models\Prospect;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Pages\CreateRecord;
use AdvisingApp\Prospect\Models\ProspectSource;
use AdvisingApp\Prospect\Models\ProspectStatus;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;

class CreateProspect extends CreateRecord
{
    protected static string $resource = ProspectResource::class;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('status_id')
                    ->label('Status')
                    ->required()
                    ->relationship('status', 'name')
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
                TextInput::make('first_name')
                    ->label('First Name')
                    ->required()
                    ->string(),
                TextInput::make('last_name')
                    ->label('Last Name')
                    ->required()
                    ->string(),
                TextInput::make(Prospect::displayNameKey())
                    ->label('Full Name')
                    ->required()
                    ->string(),
                TextInput::make('preferred')
                    ->label('Preferred Name')
                    ->string(),
                Textarea::make('description')
                    ->label('Description')
                    ->string(),
                TextInput::make('email')
                    ->label('Primary Email')
                    ->email(),
                TextInput::make('email_2')
                    ->label('Other Email')
                    ->email(),
                TextInput::make('mobile')
                    ->label('Mobile')
                    ->string(),
                Radio::make('sms_opt_out')
                    ->label('SMS Opt Out')
                    ->default(false)
                    ->boolean(),
                Radio::make('email_bounce')
                    ->label('Email Bounce')
                    ->default(false)
                    ->boolean(),
                TextInput::make('phone')
                    ->label('Other Phone')
                    ->string(),
                TextInput::make('address')
                    ->label('Address')
                    ->string(),
                TextInput::make('address_2')
                    ->label('Address 2')
                    ->string(),
                // TODO: Display this based on system configurable data format
                DatePicker::make('birthdate')
                    ->label('Birthdate')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->format('Y-m-d')
                    ->displayFormat('Y-m-d')
                    ->maxDate(now()),
                TextInput::make('hsgrad')
                    ->label('High School Graduation Date')
                    ->nullable()
                    ->numeric()
                    ->minValue(1920)
                    ->maxValue(now()->addYears(25)->year),
                Select::make('assigned_to_id')
                    ->label('Assigned To')
                    ->relationship(
                        'assignedTo',
                        'name',
                        fn (Builder $query) => $query->tap(new HasLicense(Prospect::getLicenseType())),
                    )
                    ->searchable()
                    ->nullable()
                    ->exists(
                        table: (new User())->getTable(),
                        column: (new User())->getKeyName()
                    ),
            ]);
    }
}
