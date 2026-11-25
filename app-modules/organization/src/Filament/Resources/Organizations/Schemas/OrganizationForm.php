<?php

namespace AdvisingApp\Organization\Filament\Resources\Organizations\Schemas;

use App\DataTransferObjects\AutocompletedAddress;
use App\Filament\Forms\Components\AddressInput;
use DefStudio\SearchableInput\DTO\SearchResult;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
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
            ]);
    }
}
