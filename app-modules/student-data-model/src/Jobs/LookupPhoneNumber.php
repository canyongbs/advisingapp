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

namespace AdvisingApp\StudentDataModel\Jobs;

use AdvisingApp\StudentDataModel\Contracts\PhoneNumberLookupService;
use AdvisingApp\StudentDataModel\Enums\PhoneNumberLookupStatus;
use AdvisingApp\StudentDataModel\Exceptions\PhoneNumberLookupInvalidNumber;
use AdvisingApp\StudentDataModel\Exceptions\PhoneNumberLookupRateLimited;
use AdvisingApp\StudentDataModel\Jobs\Middleware\SkipWhilePhoneNumberLookupIsRateLimited;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use DateTimeInterface;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\RateLimitedWithRedis;
use Throwable;

class LookupPhoneNumber implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $timeout = 60;

    public int $backoff = 30;

    public int $uniqueFor = 90000;

    public function __construct(
        public readonly string $phoneNumber,
    ) {}

    public function handle(PhoneNumberLookupService $phoneNumberLookupService): void
    {
        // Skip silently when no lookup provider is configured for this tenant;
        // the number simply stays unchecked and can be picked up later.
        if (! $phoneNumberLookupService->isConfigured()) {
            return;
        }

        try {
            $phoneNumberLookupService->lookup($this->phoneNumber);
        } catch (PhoneNumberLookupInvalidNumber $exception) {
            // A structurally invalid number will never resolve. Record it as
            // invalid rather than retrying and ultimately failing the job.
            PhoneNumberLookup::query()->firstOrCreate(
                ['number' => $this->phoneNumber],
                [
                    'status' => PhoneNumberLookupStatus::Invalid,
                    'raw_response' => ['error' => $exception->getMessage()],
                ],
            );
        } catch (PhoneNumberLookupRateLimited $exception) {
            // Telnyx is rate-limiting us; back off until the limit window
            // resets, per the x-ratelimit-reset header.
            $this->release($exception->secondsUntilReset);
        }
    }

    /**
     * @return array<object>
     */
    public function middleware(): array
    {
        return [
            new SkipWhilePhoneNumberLookupIsRateLimited(),
            new RateLimitedWithRedis('telnyx-number-lookup'),
        ];
    }

    public function retryUntil(): DateTimeInterface
    {
        return now()->addHours(6);
    }

    public function uniqueId(): string
    {
        return $this->phoneNumber;
    }

    public function failed(?Throwable $exception): void
    {
        if ($exception instanceof Throwable) {
            report($exception);
        }

        if (PhoneNumberLookup::query()->where('number', $this->phoneNumber)->exists()) {
            return;
        }

        PhoneNumberLookup::query()->create([
            'number' => $this->phoneNumber,
            'status' => PhoneNumberLookupStatus::LookupFailed,
            'raw_response' => [
                'error' => $exception?->getMessage(),
            ],
        ]);
    }
}
