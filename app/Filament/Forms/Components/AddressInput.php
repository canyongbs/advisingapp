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

namespace App\Filament\Forms\Components;

use App\DataTransferObjects\AutocompletedAddress;
use App\Services\AwsGeoPlacesService;
use DefStudio\SearchableInput\DTO\SearchResult;
use DefStudio\SearchableInput\Forms\Components\SearchableInput;
use Filament\Notifications\Notification;
use Throwable;

class AddressInput
{
    public static function make(): SearchableInput
    {
        return SearchableInput::make('address')
            ->label('Address')
            ->searchUsing(function (string $search) {
                if (strlen($search) < 3) {
                    return [];
                }

                try {
                    $results = app(AwsGeoPlacesService::class)->autocompleteComponents($search);

                    session()->forget('has_aws_geo_places_error_notification_sent');

                    return collect($results)->map(function (AutocompletedAddress $addressDto) {
                        return SearchResult::make($addressDto->label)->withData('data', $addressDto);
                    })->toArray();
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

                    return [];
                }
            })
            ->extraInputAttributes(['data-1p-ignore' => true, 'data-lpignore' => 'true', 'data-form-type' => 'other', 'data-bwignore' => true]);
    }
}
