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

namespace AdvisingApp\StudentDataModel\Database\Factories;

use AdvisingApp\StudentDataModel\Enums\PhoneNumberLookupStatus;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PhoneNumberLookup>
 */
class PhoneNumberLookupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Produces an internally consistent successful carrier lookup: a random
     * Telnyx `carrier.type`, with `status` and `raw_response` derived from it.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $number = $this->faker->unique()->e164PhoneNumber();
        $carrierName = $this->faker->company();
        $carrierType = $this->faker->randomElement(['mobile', 'fixed line', 'voip', 'toll free']);

        return [
            'number' => $number,
            'status' => PhoneNumberLookupStatus::fromTelnyxCarrierType($carrierType),
            'carrier_name' => $carrierName,
            'carrier_type' => $carrierType,
            'raw_response' => [
                'data' => [
                    'record_type' => 'number_lookup',
                    'phone_number' => $number,
                    'country_code' => 'US',
                    'carrier' => [
                        'name' => $carrierName,
                        'type' => $carrierType,
                        'error_code' => null,
                        'mobile_country_code' => 'US',
                        'mobile_network_code' => $this->faker->numerify('###'),
                    ],
                ],
            ],
        ];
    }

    /**
     * @return Factory<PhoneNumberLookup>
     */
    public function mobile(): Factory
    {
        return $this->withCarrierType('mobile');
    }

    /**
     * @return Factory<PhoneNumberLookup>
     */
    public function landline(): Factory
    {
        return $this->withCarrierType('fixed line');
    }

    /**
     * @return Factory<PhoneNumberLookup>
     */
    public function voip(): Factory
    {
        return $this->withCarrierType('voip');
    }

    /**
     * @return Factory<PhoneNumberLookup>
     */
    public function tollFree(): Factory
    {
        return $this->withCarrierType('toll free');
    }

    /**
     * A successful lookup whose carrier type does not map to a known line type.
     *
     * @return Factory<PhoneNumberLookup>
     */
    public function unknown(): Factory
    {
        return $this->withCarrierType($this->faker->randomElement(['voicemail', 'pager', 'unknown']));
    }

    /**
     * A completed lookup where Telnyx reported the number as invalid.
     *
     * @return Factory<PhoneNumberLookup>
     */
    public function invalid(): Factory
    {
        return $this->state([
            'status' => PhoneNumberLookupStatus::Invalid,
            'carrier_name' => null,
            'carrier_type' => null,
            'raw_response' => [
                'errors' => [
                    [
                        'code' => '10001',
                        'title' => 'Invalid phone number',
                    ],
                ],
            ],
        ]);
    }

    /**
     * A lookup that could not be completed due to a provider/API error.
     *
     * @return Factory<PhoneNumberLookup>
     */
    public function lookupFailed(): Factory
    {
        return $this->state([
            'status' => PhoneNumberLookupStatus::LookupFailed,
            'carrier_name' => null,
            'carrier_type' => null,
            'raw_response' => [
                'error' => 'Telnyx API request failed.',
            ],
        ]);
    }

    /**
     * @return Factory<PhoneNumberLookup>
     */
    protected function withCarrierType(string $carrierType): Factory
    {
        return $this->state(fn (array $attributes): array => [
            'status' => PhoneNumberLookupStatus::fromTelnyxCarrierType($carrierType),
            'carrier_type' => $carrierType,
            'raw_response' => [
                'data' => [
                    'record_type' => 'number_lookup',
                    'phone_number' => $attributes['number'],
                    'country_code' => 'US',
                    'carrier' => [
                        'name' => $attributes['carrier_name'],
                        'type' => $carrierType,
                        'error_code' => null,
                        'mobile_country_code' => 'US',
                        'mobile_network_code' => $this->faker->numerify('###'),
                    ],
                ],
            ],
        ]);
    }
}
