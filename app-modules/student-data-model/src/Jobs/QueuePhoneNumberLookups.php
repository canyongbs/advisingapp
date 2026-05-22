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

use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\StudentDataModel\Contracts\PhoneNumberLookupService;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Aggregate scan job: finds the distinct set of Student and Prospect phone
 * numbers that have never been looked up and dispatches an individual
 * {@see LookupPhoneNumber} job for each. Triggered after a SIS sync, after a
 * relevant import, and once from the deploy data migration that backfills the
 * feature.
 */
class QueuePhoneNumberLookups implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $timeout = 1800;

    public int $tries = 1;

    /**
     * Only one aggregate scan needs to run at a time; the cache is
     * tenant-prefixed, so this is effectively one scan per tenant.
     */
    public int $uniqueFor = 3600;

    public function handle(PhoneNumberLookupService $phoneNumberLookupService): void
    {
        if (! $phoneNumberLookupService->isConfigured()) {
            return;
        }

        $studentNumbers = StudentPhoneNumber::query()
            ->select('number')
            ->whereNotNull('number')
            ->where('number', '!=', '');

        $prospectNumbers = ProspectPhoneNumber::query()
            ->select('number')
            ->whereNotNull('number')
            ->where('number', '!=', '');

        // A UNION yields the distinct set of numbers across both tables, so
        // each number is dispatched exactly once. Chunking by the number
        // column keeps memory flat on large datasets.
        DB::query()
            ->fromSub($studentNumbers->union($prospectNumbers), 'phone_numbers')
            ->whereNotIn('number', PhoneNumberLookup::query()->select('number'))
            ->chunkById(1000, function (Collection $phoneNumbers): void {
                foreach ($phoneNumbers as $phoneNumber) {
                    LookupPhoneNumber::dispatch($phoneNumber->number);
                }
            }, 'number');
    }
}
