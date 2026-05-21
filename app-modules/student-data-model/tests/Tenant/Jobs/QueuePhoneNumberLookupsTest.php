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

use AdvisingApp\Prospect\Imports\ProspectImporter;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Prospect\Models\ProspectPhoneNumber;
use AdvisingApp\StudentDataModel\Contracts\PhoneNumberLookupService;
use AdvisingApp\StudentDataModel\Events\SisSyncCompleted;
use AdvisingApp\StudentDataModel\Filament\Imports\StudentImporter;
use AdvisingApp\StudentDataModel\Jobs\LookupPhoneNumber;
use AdvisingApp\StudentDataModel\Jobs\QueuePhoneNumberLookups;
use AdvisingApp\StudentDataModel\Models\PhoneNumberLookup;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\StudentDataModel\Models\StudentPhoneNumber;
use Filament\Actions\Imports\Events\ImportCompleted;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Bus;

function configuredLookupService(bool $configured = true): PhoneNumberLookupService
{
    return new class ($configured) implements PhoneNumberLookupService {
        public function __construct(private bool $configured) {}

        public function isConfigured(): bool
        {
            return $this->configured;
        }

        public function lookup(string $phoneNumber): PhoneNumberLookup
        {
            return new PhoneNumberLookup();
        }
    };
}

it('queues a lookup for student and prospect numbers that have no existing result', function () {
    Bus::fake([LookupPhoneNumber::class]);

    $student = Student::factory()->create();

    // createQuietly() skips the observer so only the job under test dispatches.
    StudentPhoneNumber::factory()->createQuietly([
        'sisid' => $student->sisid,
        'number' => '+16502530001',
        'order' => 1,
    ]);
    StudentPhoneNumber::factory()->createQuietly([
        'sisid' => $student->sisid,
        'number' => '+16502530002',
        'order' => 2,
    ]);

    $prospect = Prospect::factory()->create();

    ProspectPhoneNumber::factory()->createQuietly([
        'prospect_id' => $prospect->getKey(),
        'number' => '+16502530003',
        'order' => 1,
    ]);
    ProspectPhoneNumber::factory()->createQuietly([
        'prospect_id' => $prospect->getKey(),
        'number' => '+16502530004',
        'order' => 2,
    ]);

    // One student number and one prospect number have already been checked.
    PhoneNumberLookup::factory()->create(['number' => '+16502530002']);
    PhoneNumberLookup::factory()->create(['number' => '+16502530004']);

    (new QueuePhoneNumberLookups())->handle(configuredLookupService());

    expect(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502530001'))->toHaveCount(1)
        ->and(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502530003'))->toHaveCount(1)
        ->and(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502530002'))->toHaveCount(0)
        ->and(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502530004'))->toHaveCount(0);
});

it('does not scan when the lookup provider is not configured', function () {
    Bus::fake([LookupPhoneNumber::class]);

    $student = Student::factory()->create();
    StudentPhoneNumber::factory()->createQuietly([
        'sisid' => $student->sisid,
        'number' => '+16502530009',
        'order' => 1,
    ]);

    (new QueuePhoneNumberLookups())->handle(configuredLookupService(configured: false));

    expect(Bus::dispatched(LookupPhoneNumber::class, fn (LookupPhoneNumber $job) => $job->phoneNumber === '+16502530009'))
        ->toHaveCount(0);
});

it('dispatches the aggregate job when a SIS sync completes', function () {
    Bus::fake([QueuePhoneNumberLookups::class]);

    SisSyncCompleted::dispatch();

    Bus::assertDispatched(QueuePhoneNumberLookups::class);
});

it('dispatches the aggregate job when a prospect import completes', function () {
    Bus::fake([QueuePhoneNumberLookups::class]);

    $import = new Import();
    $import->importer = ProspectImporter::class;

    ImportCompleted::dispatch($import, [], []);

    Bus::assertDispatched(QueuePhoneNumberLookups::class);
});

it('does not dispatch the aggregate job for an unrelated import', function () {
    Bus::fake([QueuePhoneNumberLookups::class]);

    $import = new Import();
    $import->importer = StudentImporter::class;

    ImportCompleted::dispatch($import, [], []);

    Bus::assertNotDispatched(QueuePhoneNumberLookups::class);
});
