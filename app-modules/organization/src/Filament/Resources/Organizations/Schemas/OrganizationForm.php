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
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Organization\Filament\Resources\Organizations\Schemas;

use AdvisingApp\Organization\Models\Organization;
use AdvisingApp\Organization\Rules\UniqueOrganizationDomain;
use App\DataTransferObjects\AutocompletedAddress;
use App\Filament\Forms\Components\AddressInput;
use DefStudio\SearchableInput\DTO\SearchResult;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Unique;
use Throwable;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class OrganizationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Details')
                    ->schema([
                        TextInput::make('name')
                            ->label('Name')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true, modifyRuleUsing: fn (Unique $rule): Unique => $rule->withoutTrashed()),
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255),
                        PhoneInput::make('phone_number')
                            ->label('Phone Number'),
                        TextInput::make('website')
                            ->label('Website')
                            ->url()
                            ->maxLength(255),
                        Textarea::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Address')
                    ->schema([
                        AddressInput::make()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->onItemSelected(function (SearchResult $item, ?Set $set = null): void {
                                assert($set instanceof Set);

                                try {
                                    $data = $item->get('data');

                                    if (! $data instanceof AutocompletedAddress) {
                                        $data = AutocompletedAddress::from($data);
                                    }

                                    $set('address', $data->line1);
                                    $set('city', $data->city);
                                    $set('state', $data->state);
                                    $set('postalcode', $data->postalCode);
                                    $set('country', $data->country);
                                } catch (Throwable $exception) {
                                    if (! session()->has('has_aws_geo_places_error_notification_sent')) {
                                        Notification::make()
                                            ->title('Failed to fetch address suggestions')
                                            ->body('An error occurred while fetching address suggestions. Please try again later.')
                                            ->danger()
                                            ->send();

                                        session()->put('has_aws_geo_places_error_notification_sent', true);
                                    }

                                    report($exception);
                                }
                            }),
                        TextInput::make('city')
                            ->label('City')
                            ->maxLength(255),
                        TextInput::make('state')
                            ->label('State')
                            ->maxLength(255),
                        TextInput::make('postalcode')
                            ->label('Postal Code')
                            ->maxLength(255),
                        TextInput::make('country')
                            ->label('Country')
                            ->maxLength(255),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Section::make('Domains')
                    ->schema([
                        Repeater::make('domains')
                            ->hiddenLabel()
                            ->table([
                                TableColumn::make('Domain'),
                            ])
                            ->schema([
                                TextInput::make('domain')
                                    ->label('Domain')
                                    ->required()
                                    ->maxLength(255)
                                    ->regex(Organization::DOMAIN_PATTERN)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn (?string $state, Set $set): mixed => $set('domain', filled($state) ? Str::lower($state) : $state))
                                    ->distinct()
                                    ->validationMessages([
                                        'distinct' => 'This :attribute is already in use and may not be used a second time.',
                                    ])
                                    ->rule(fn (?Organization $record): UniqueOrganizationDomain => new UniqueOrganizationDomain($record?->getKey())),
                            ])
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->addActionLabel('Add domain'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
