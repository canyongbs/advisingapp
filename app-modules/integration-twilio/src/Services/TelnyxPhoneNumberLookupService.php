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

namespace AdvisingApp\IntegrationTwilio\Services;

use AdvisingApp\IntegrationTwilio\Settings\TwilioSettings;
use AdvisingApp\StudentDataModel\Actions\NormalizePhoneNumberToE164;
use AdvisingApp\StudentDataModel\Contracts\PhoneNumberLookupService;
use AdvisingApp\StudentDataModel\Enums\PhoneNumberLookupStatus;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use RuntimeException;
use Telnyx\Exception\ApiErrorException;
use Telnyx\NumberLookup;
use Telnyx\Telnyx;

class TelnyxPhoneNumberLookupService implements PhoneNumberLookupService
{
    public function __construct(
        protected NormalizePhoneNumberToE164 $normalizePhoneNumberToE164,
    ) {}

    public function lookup(string $phoneNumber): PhoneNumberLookup
    {
        // re-validate the E.164 format regardless of where
        // the number originated. Throws InvalidArgumentException when invalid.
        $normalizedNumber = ($this->normalizePhoneNumberToE164)($phoneNumber);

        // never look up the same normalized number twice.
        $existingLookup = PhoneNumberLookup::query()
            ->where('number', $normalizedNumber)
            ->first();

        if ($existingLookup) {
            return $existingLookup;
        }

        return $this->performLookup($normalizedNumber);
    }

    protected function performLookup(string $normalizedNumber): PhoneNumberLookup
    {
        $apiKey = app(TwilioSettings::class)->telnyx_api_key;

        if (blank($apiKey)) {
            throw new RuntimeException('Cannot perform a Telnyx number lookup: the Telnyx API key is not configured.');
        }

        Telnyx::setApiKey($apiKey);

        try {
            // "carrier" lookup only. We deliberately do not request
            // "caller-name", as CNAM enrichment is out of scope.
            $result = NumberLookup::retrieve([
                'id' => $normalizedNumber,
                'type' => ['carrier'],
            ]);
        } catch (ApiErrorException $exception) {
            // A 404 means Telnyx could not recognize the number at all, which
            // is a definitive result: the number is invalid.
            if ($exception->getHttpStatus() === 404) {
                return $this->store(
                    number: $normalizedNumber,
                    status: PhoneNumberLookupStatus::Invalid,
                    carrierName: null,
                    carrierType: null,
                    rawResponse: $exception->getJsonBody() ?? ['error' => $exception->getMessage()],
                );
            }

            // Any other API error (auth, rate limit, server error, connection
            // failure) is operational and potentially transient. Re-throw so
            // the queued job can retry it; a persistent failure is recorded as
            // lookup_failed by the job's failed() handler.
            throw $exception;
        }

        $data = $result->toArray();
        $carrier = $data['carrier'] ?? [];

        $carrierName = $carrier['name'] ?? null;
        $carrierType = $carrier['type'] ?? null;

        return $this->store(
            number: $normalizedNumber,
            status: PhoneNumberLookupStatus::fromTelnyxCarrierType($carrierType),
            carrierName: $carrierName,
            carrierType: $carrierType,
            rawResponse: $this->rawResponse($result),
        );
    }

    /**
     * @param array<mixed>|null $rawResponse
     */
    protected function store(
        string $number,
        PhoneNumberLookupStatus $status,
        ?string $carrierName,
        ?string $carrierType,
        ?array $rawResponse,
    ): PhoneNumberLookup {
        return PhoneNumberLookup::query()->firstOrCreate(
            ['number' => $number],
            [
                'status' => $status,
                'carrier_name' => $carrierName,
                'carrier_type' => $carrierType,
                'raw_response' => $rawResponse,
            ],
        );
    }

    /**
     * @return array<mixed>
     */
    protected function rawResponse(NumberLookup $result): array
    {
        $lastResponse = $result->getLastResponse();

        if ($lastResponse && filled($lastResponse->body)) {
            $decoded = json_decode($lastResponse->body, true);

            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return $result->toArray();
    }
}
