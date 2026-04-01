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

namespace App\Services;

use App\DataTransferObjects\AutocompletedAddress;
use Aws\GeoPlaces\GeoPlacesClient;
use Exception;
use Throwable;

class AwsGeoPlacesService
{
    public function __construct(
        protected GeoPlacesClient $client,
    ) {}

    /**
     * @return array<int, AutocompletedAddress>
     */
    public function autocompleteComponents(string $query): array
    {
        try {
            $result = $this->client->autocomplete([
                'QueryText' => $query,
                'MaxResults' => 10,
                'AdditionalFeatures' => ['Core'],
            ]);
        } catch (Throwable $exception) {
            report(new Exception('AWS GeoPlaces autocomplete failed', previous: $exception));

            return [];
        }

        /** @var array<int, array{Address?: array{Label?: string, Country?: array{Code2?: string, Code3?: string, Name?: string}, Region?: array{Code?: string, Name?: string}, SubRegion?: array{Name?: string}, Locality?: string, District?: string, SubDistrict?: string, PostalCode?: string, AddressNumber?: string, Street?: string, Building?: string}, Title: string}> $items */
        $items = $result['ResultItems'] ?? [];

        return collect($items)
            ->map(function (array $item): AutocompletedAddress {
                $address = $item['Address'] ?? [];
                $countryCode = $address['Country']['Code2'] ?? $address['Country']['Code3'] ?? '';

                return new AutocompletedAddress(
                    line1: trim(($address['AddressNumber'] ?? '') . ' ' . ($address['Street'] ?? '')),
                    city: $this->resolveCity($address, $countryCode),
                    state: $this->resolveState($address),
                    postalCode: $address['PostalCode'] ?? '',
                    country: $countryCode,
                    label: $address['Label'] ?? $item['Title'],
                );
            })
            ->values()
            ->all();
    }

    /**
     * @param array<string, mixed> $address
     */
    private function resolveCity(array $address, string $countryCode): string
    {
        return match ($countryCode) {
            'MX' => $address['Locality'] ?? $address['SubRegion']['Name'] ?? $address['District'] ?? '',
            'CA' => $address['Locality'] ?? $address['District'] ?? '',
            'GB' => $address['Locality'] ?? $address['District'] ?? $address['SubDistrict'] ?? '',
            default => $address['Locality'] ?? $address['District'] ?? $address['SubDistrict'] ?? '',
        };
    }

    /**
     * @param array<string, mixed> $address
     */
    private function resolveState(array $address): string
    {
        return $address['Region']['Code'] ?? $address['Region']['Name'] ?? '';
    }
}
