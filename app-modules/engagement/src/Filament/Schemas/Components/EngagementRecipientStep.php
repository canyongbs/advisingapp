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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

namespace AdvisingApp\Engagement\Filament\Schemas\Components;

use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectEmailAddress;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\StudentDataModel\Enums\EmailAddressOptInOptOutStatus;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentEmailAddress;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use App\Filament\Forms\Components\EducatableSelect;
use Closure;
use Exception;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Support\Str;

class EngagementRecipientStep
{
    /**
     * @param  Closure(): (Student|Prospect|null)  $getEducatable
     * @param  Closure(Get): (Student|Prospect|null)  $resolveEducatable
     */
    public static function make(
        Closure $getEducatable,
        Closure $resolveEducatable,
    ): Step {
        return Step::make('Recipient Details')
            ->schema([
                ...($getEducatable)() ? [] : [
                    EducatableSelect::make(
                        name: 'recipient',
                        isExcludingConvertedProspects: true,
                        modifyKeySelectUsing: function (Select $select): Select {
                            return $select->disableOptionWhen(function (string $value): bool {
                                static $noContactCache = [];
                                $cacheKey = $value;

                                if (! array_key_exists($cacheKey, $noContactCache)) {
                                    $educatable = Student::find($value) ?? Prospect::find($value);
                                    $noContactCache[$cacheKey] = $educatable
                                        ? ! $educatable->hasAnyValidContactRoute()
                                        : false;
                                }

                                return $noContactCache[$cacheKey];
                            });
                        },
                    )
                        ->label('Recipient Info')
                        ->live()
                        ->typeSelectToggleButtons()
                        ->required()
                        ->columns(2)
                        ->afterStateUpdated(function (Get $get, Set $set) {
                            $educatable = match ($get('recipient_type')) {
                                'student' => Student::find($get('recipient_id')),
                                'prospect' => Str::isUuid($get('recipient_id')) ? Prospect::find($get('recipient_id')) : null,
                                default => null,
                            };

                            if (! $educatable) {
                                return;
                            }

                            $defaultChannel = $educatable->getDefaultEngagementChannel();

                            if ($defaultChannel) {
                                $set('channel', $defaultChannel->value);
                                $set('recipient_route_id', $educatable->getDefaultRouteForEngagementChannel($defaultChannel));

                                return;
                            }
                        }),
                ],
                Grid::make(2)
                    ->schema(function (Get $get) use ($resolveEducatable): array {
                        return [
                            Fieldset::make('Message Type')
                                ->schema([
                                    ToggleButtons::make('channel')
                                        ->options(function (Get $get) use ($resolveEducatable): array {
                                            $educatable = $resolveEducatable($get);

                                            if (! $educatable) {
                                                return [];
                                            }

                                            return array_filter(
                                                NotificationChannel::getAvailableEngagementOptions(),
                                                function (string $channelValue) use ($educatable): bool {
                                                    $channel = NotificationChannel::tryFrom($channelValue);

                                                    if ($channel?->getCaseDisabled() ?? false) {
                                                        return false;
                                                    }

                                                    return match ($channel) {
                                                        NotificationChannel::Email => $educatable->hasValidEmail(),
                                                        NotificationChannel::Sms => $educatable->hasValidSms(),
                                                        default => true,
                                                    };
                                                },
                                                ARRAY_FILTER_USE_KEY
                                            );
                                        })
                                        ->default(NotificationChannel::Email->value)
                                        ->inline()
                                        ->live()
                                        ->afterStateUpdated(function (mixed $state, Get $get, Set $set) use ($resolveEducatable): void {
                                            $educatable = $resolveEducatable($get);

                                            if (! $educatable) {
                                                return;
                                            }

                                            $channel = NotificationChannel::parse($state);
                                            $set('recipient_route_id', $educatable->getDefaultRouteForEngagementChannel($channel));
                                        }),
                                    Select::make('recipient_route_id')
                                        ->label(fn (Get $get): string => match (NotificationChannel::parse($get('channel'))) {
                                            NotificationChannel::Email => 'Email address',
                                            NotificationChannel::Sms => 'Phone number',
                                            default => throw new Exception('Invalid channel.'),
                                        })
                                        ->options(function (Get $get) use ($resolveEducatable): array {
                                            $educatable = $resolveEducatable($get);

                                            if (! $educatable) {
                                                return [];
                                            }

                                            return match (NotificationChannel::parse($get('channel'))) {
                                                NotificationChannel::Email => $educatable->emailAddresses()
                                                    ->whereDoesntHave('bounced')
                                                    ->whereDoesntHave('optedOut', fn ($query) => $query->where('status', EmailAddressOptInOptOutStatus::OptedOut))
                                                    ->get()
                                                    ->mapWithKeys(fn (StudentEmailAddress | ProspectEmailAddress $emailAddress): array => [
                                                        $emailAddress->getKey() => $emailAddress->address . (filled($emailAddress->type) ? " ({$emailAddress->type})" : ''),
                                                    ])
                                                    ->all(),
                                                NotificationChannel::Sms => $educatable->phoneNumbers()
                                                    ->where('can_receive_sms', true)
                                                    ->whereDoesntHave('smsOptOut')
                                                    ->whereDoesntHave('bounced')
                                                    ->get()
                                                    ->mapWithKeys(fn (StudentPhoneNumber | ProspectPhoneNumber $phoneNumber): array => [
                                                        $phoneNumber->getKey() => $phoneNumber->number . (filled($phoneNumber->ext) ? " (ext. {$phoneNumber->ext})" : '') . (filled($phoneNumber->type) ? " ({$phoneNumber->type})" : ''),
                                                    ])
                                                    ->all(),
                                                default => [],
                                            };
                                        })
                                        ->disabled(function (Get $get) use ($resolveEducatable): bool {
                                            $educatable = $resolveEducatable($get);

                                            return blank($educatable);
                                        })
                                        ->required(),
                                ])
                                ->visible(function (Get $get) use ($resolveEducatable): bool {
                                    $educatable = $resolveEducatable($get);

                                    return $educatable !== null && $educatable->hasAnyValidContactRoute();
                                })
                                ->columnSpanFull(),
                            Text::make('This recipient does not have valid contact information. Please select a different recipient.')
                                ->visible(function (Get $get) use ($resolveEducatable): bool {
                                    $educatable = $resolveEducatable($get);

                                    return $educatable !== null && ! $educatable->hasAnyValidContactRoute();
                                })
                                ->columnSpanFull(),
                        ];
                    })
                    ->visible(fn (Get $get) => ! is_null(($getEducatable)()) || $get('recipient_id')),
            ]);
    }
}
