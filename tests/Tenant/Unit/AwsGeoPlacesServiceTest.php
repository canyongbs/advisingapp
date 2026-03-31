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
      of the licensor in the software. Any use of the licensor's trademarks is subject
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

use App\Services\AwsGeoPlacesService;
use Aws\GeoPlaces\GeoPlacesClient;
use Aws\Result;
use Mockery\MockInterface;

it('maps US addresses using Country.Code2 and Region.Code', function () {
    /** @phpstan-ignore method.notFound */
    $client = $this->mock(GeoPlacesClient::class, function (MockInterface $mock) {
        /** @phpstan-ignore method.notFound */
        $mock->shouldReceive('autocomplete')
            ->once()
            ->andReturn(new Result([
                'ResultItems' => [
                    [
                        'Title' => '123 Main St, Austin, TX',
                        'Address' => [
                            'Label' => '123 Main St, Austin, TX, 78701, US',
                            'AddressNumber' => '123',
                            'Street' => 'Main St',
                            'Locality' => 'Austin',
                            'Region' => [
                                'Code' => 'TX',
                                'Name' => 'Texas',
                            ],
                            'PostalCode' => '78701',
                            'Country' => [
                                'Code2' => 'US',
                                'Code3' => 'USA',
                                'Name' => 'United States',
                            ],
                        ],
                    ],
                ],
            ]));
    });

    $service = new AwsGeoPlacesService($client);
    $results = $service->autocompleteComponents('123 Main');

    expect($results)->toHaveCount(1);
    expect($results[0]->line1)->toBe('123 Main St');
    expect($results[0]->city)->toBe('Austin');
    expect($results[0]->state)->toBe('TX');
    expect($results[0]->postalCode)->toBe('78701');
    expect($results[0]->country)->toBe('US');
    expect($results[0]->label)->toBe('123 Main St, Austin, TX, 78701, US');
});

it('maps Canadian addresses using Region.Code for province', function () {
    /** @phpstan-ignore method.notFound */
    $client = $this->mock(GeoPlacesClient::class, function (MockInterface $mock) {
        /** @phpstan-ignore method.notFound */
        $mock->shouldReceive('autocomplete')
            ->once()
            ->andReturn(new Result([
                'ResultItems' => [
                    [
                        'Title' => '100 Wellington St, Ottawa, ON',
                        'Address' => [
                            'Label' => '100 Wellington St, Ottawa, ON, K1A 0A9, CA',
                            'AddressNumber' => '100',
                            'Street' => 'Wellington St',
                            'Locality' => 'Ottawa',
                            'Region' => [
                                'Code' => 'ON',
                                'Name' => 'Ontario',
                            ],
                            'PostalCode' => 'K1A 0A9',
                            'Country' => [
                                'Code2' => 'CA',
                                'Code3' => 'CAN',
                                'Name' => 'Canada',
                            ],
                        ],
                    ],
                ],
            ]));
    });

    $service = new AwsGeoPlacesService($client);
    $results = $service->autocompleteComponents('100 Wellington');

    expect($results)->toHaveCount(1);
    expect($results[0]->city)->toBe('Ottawa');
    expect($results[0]->state)->toBe('ON');
    expect($results[0]->country)->toBe('CA');
    expect($results[0]->postalCode)->toBe('K1A 0A9');
});

it('falls back to District for city when Locality is absent in Canadian addresses', function () {
    /** @phpstan-ignore method.notFound */
    $client = $this->mock(GeoPlacesClient::class, function (MockInterface $mock) {
        /** @phpstan-ignore method.notFound */
        $mock->shouldReceive('autocomplete')
            ->once()
            ->andReturn(new Result([
                'ResultItems' => [
                    [
                        'Title' => '200 Some Rd, Vancouver, BC',
                        'Address' => [
                            'Label' => '200 Some Rd, Vancouver, BC, V5K 0A1, CA',
                            'AddressNumber' => '200',
                            'Street' => 'Some Rd',
                            'District' => 'Vancouver',
                            'Region' => [
                                'Code' => 'BC',
                                'Name' => 'British Columbia',
                            ],
                            'PostalCode' => 'V5K 0A1',
                            'Country' => [
                                'Code2' => 'CA',
                                'Code3' => 'CAN',
                                'Name' => 'Canada',
                            ],
                        ],
                    ],
                ],
            ]));
    });

    $service = new AwsGeoPlacesService($client);
    $results = $service->autocompleteComponents('200 Some Rd');

    expect($results[0]->city)->toBe('Vancouver');
    expect($results[0]->state)->toBe('BC');
    expect($results[0]->country)->toBe('CA');
});

it('maps Mexican addresses with SubRegion fallback for city', function () {
    /** @phpstan-ignore method.notFound */
    $client = $this->mock(GeoPlacesClient::class, function (MockInterface $mock) {
        /** @phpstan-ignore method.notFound */
        $mock->shouldReceive('autocomplete')
            ->once()
            ->andReturn(new Result([
                'ResultItems' => [
                    [
                        'Title' => '50 Reforma Ave, Mexico City',
                        'Address' => [
                            'Label' => '50 Reforma Ave, Mexico City, CDMX, 06600, MX',
                            'AddressNumber' => '50',
                            'Street' => 'Reforma Ave',
                            'SubRegion' => [
                                'Name' => 'Mexico City',
                            ],
                            'Region' => [
                                'Code' => 'CDMX',
                                'Name' => 'Ciudad de México',
                            ],
                            'PostalCode' => '06600',
                            'Country' => [
                                'Code2' => 'MX',
                                'Code3' => 'MEX',
                                'Name' => 'Mexico',
                            ],
                        ],
                    ],
                ],
            ]));
    });

    $service = new AwsGeoPlacesService($client);
    $results = $service->autocompleteComponents('50 Reforma');

    expect($results[0]->city)->toBe('Mexico City');
    expect($results[0]->state)->toBe('CDMX');
    expect($results[0]->country)->toBe('MX');
});

it('maps UK addresses with District fallback for city', function () {
    /** @phpstan-ignore method.notFound */
    $client = $this->mock(GeoPlacesClient::class, function (MockInterface $mock) {
        /** @phpstan-ignore method.notFound */
        $mock->shouldReceive('autocomplete')
            ->once()
            ->andReturn(new Result([
                'ResultItems' => [
                    [
                        'Title' => '10 Downing St, London',
                        'Address' => [
                            'Label' => '10 Downing St, London, SW1A 2AA, GB',
                            'AddressNumber' => '10',
                            'Street' => 'Downing St',
                            'District' => 'London',
                            'Region' => [
                                'Code' => 'ENG',
                                'Name' => 'England',
                            ],
                            'PostalCode' => 'SW1A 2AA',
                            'Country' => [
                                'Code2' => 'GB',
                                'Code3' => 'GBR',
                                'Name' => 'United Kingdom',
                            ],
                        ],
                    ],
                ],
            ]));
    });

    $service = new AwsGeoPlacesService($client);
    $results = $service->autocompleteComponents('10 Downing');

    expect($results[0]->city)->toBe('London');
    expect($results[0]->state)->toBe('ENG');
    expect($results[0]->country)->toBe('GB');
});

it('falls back to Code3 when Code2 is absent', function () {
    /** @phpstan-ignore method.notFound */
    $client = $this->mock(GeoPlacesClient::class, function (MockInterface $mock) {
        /** @phpstan-ignore method.notFound */
        $mock->shouldReceive('autocomplete')
            ->once()
            ->andReturn(new Result([
                'ResultItems' => [
                    [
                        'Title' => '1 Test Ave',
                        'Address' => [
                            'Label' => '1 Test Ave, TestCity, TS, 00000',
                            'AddressNumber' => '1',
                            'Street' => 'Test Ave',
                            'Locality' => 'TestCity',
                            'Region' => [
                                'Name' => 'TestState',
                            ],
                            'PostalCode' => '00000',
                            'Country' => [
                                'Code3' => 'TST',
                                'Name' => 'Testland',
                            ],
                        ],
                    ],
                ],
            ]));
    });

    $service = new AwsGeoPlacesService($client);
    $results = $service->autocompleteComponents('1 Test');

    expect($results[0]->country)->toBe('TST');
    expect($results[0]->state)->toBe('TestState');
});

it('falls back to Region.Name when Region.Code is absent', function () {
    /** @phpstan-ignore method.notFound */
    $client = $this->mock(GeoPlacesClient::class, function (MockInterface $mock) {
        /** @phpstan-ignore method.notFound */
        $mock->shouldReceive('autocomplete')
            ->once()
            ->andReturn(new Result([
                'ResultItems' => [
                    [
                        'Title' => '5 High St',
                        'Address' => [
                            'Label' => '5 High St, SomeCity',
                            'AddressNumber' => '5',
                            'Street' => 'High St',
                            'Locality' => 'SomeCity',
                            'Region' => [
                                'Name' => 'SomeProvince',
                            ],
                            'PostalCode' => '99999',
                            'Country' => [
                                'Code2' => 'XX',
                                'Name' => 'Unknown',
                            ],
                        ],
                    ],
                ],
            ]));
    });

    $service = new AwsGeoPlacesService($client);
    $results = $service->autocompleteComponents('5 High');

    expect($results[0]->state)->toBe('SomeProvince');
});

it('returns empty strings when address fields are missing', function () {
    /** @phpstan-ignore method.notFound */
    $client = $this->mock(GeoPlacesClient::class, function (MockInterface $mock) {
        /** @phpstan-ignore method.notFound */
        $mock->shouldReceive('autocomplete')
            ->once()
            ->andReturn(new Result([
                'ResultItems' => [
                    [
                        'Title' => 'Sparse Address',
                        'Address' => [
                            'Country' => [
                                'Code2' => 'US',
                            ],
                        ],
                    ],
                ],
            ]));
    });

    $service = new AwsGeoPlacesService($client);
    $results = $service->autocompleteComponents('sparse');

    expect($results)->toHaveCount(1);
    expect($results[0]->line1)->toBe('');
    expect($results[0]->city)->toBe('');
    expect($results[0]->state)->toBe('');
    expect($results[0]->postalCode)->toBe('');
    expect($results[0]->country)->toBe('US');
    expect($results[0]->label)->toBe('Sparse Address');
});
