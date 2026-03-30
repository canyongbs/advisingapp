<?php

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
     * @return array<int, string>
     */
    public function autocomplete(string $query): array
    {
        try {
            $result = $this->client->autocomplete([
                'QueryText' => $query,
                'MaxResults' => 10,
            ]);
        } catch (Throwable $exception) {
            report(new Exception('AWS GeoPlaces autocomplete failed', previous: $exception));

            return [];
        }

        /** @var array<int, array{Address?: array{Label?: string}, Title: string}> $items */
        $items = $result['ResultItems'] ?? [];

        return collect($items)
            ->map(fn (array $item): string => $item['Address']['Label'] ?? $item['Title'])
            ->values()
            ->all();
    }

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
            ->map(fn (array $item): AutocompletedAddress => new AutocompletedAddress(
                line1: trim(($item['Address']['AddressNumber'] ?? '') . ' ' . ($item['Address']['Street'] ?? '')),
                city: $item['Address']['Locality'] ?? '',
                state: $item['Address']['Region']['Name'] ?? '',
                postalCode: $item['Address']['PostalCode'] ?? '',
                country: $item['Address']['Country']['Name'] ?? '',
                label: $item['Address']['Label'] ?? $item['Title'],
            ))
            ->values()
            ->all();
    }
}
