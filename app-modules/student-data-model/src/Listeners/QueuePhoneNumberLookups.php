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

namespace AdvisingApp\StudentDataModel\Listeners;

use AdvisingApp\StudentDataModel\Events\SisSyncCompleted;
use AdvisingApp\StudentDataModel\Jobs\LookupPhoneNumber;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

class QueuePhoneNumberLookups implements ShouldQueue
{
    public int $timeout = 1800;

    public int $tries = 1;

    /**
     * Queue a lookup for every Student phone number that has never been
     * checked. Runs queued so it never blocks the SIS sync request.
     *
     * The scan is chunked to keep memory flat on large datasets. The same
     * number may appear on multiple rows; LookupPhoneNumber is a unique job,
     * so duplicate dispatches collapse to a single lookup.
     */
    public function handle(SisSyncCompleted $event): void
    {
        StudentPhoneNumber::query()
            ->whereNotNull('number')
            ->where('number', '!=', '')
            ->whereNotIn('number', PhoneNumberLookup::query()->select('number'))
            ->chunkById(1000, function (Collection $studentPhoneNumbers): void {
                foreach ($studentPhoneNumbers as $studentPhoneNumber) {
                    LookupPhoneNumber::dispatch($studentPhoneNumber->number);
                }
            });
    }
}
