<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\Segment\Models\Segment;
use Illuminate\Support\Facades\Artisan;
use AdvisingApp\Campaign\Models\Campaign;

use function Tests\Helpers\rollbackToBefore;

use AdvisingApp\CaseloadManagement\Models\Caseload;
use AdvisingApp\CaseloadManagement\Models\CaseloadSubject;

it('creates segments and all necessary relations from existing caseloads', function () {
    rollbackToBefore('2024_06_24_125455_data_migrate_caseloads_to_segments_table');

    // Given that we have a caseload
    $caseload = Caseload::factory()->createQuietly();

    // And the caseload has subjects
    $caseloadSubjects = CaseloadSubject::factory()->count(3)->create([
        'caseload_id' => $caseload->id,
    ]);

    // And the caseload has campaigns
    $campaigns = Campaign::factory()->count(3)->create([
        'caseload_id' => $caseload->id,
        'segment_id' => null,
    ]);

    expect(Segment::count())->toBe(0);

    // When we run the migration to migrate caseloads to segments
    Artisan::call('migrate', ['--step' => 1]);

    ray(Segment::all());
    // There should be a segment created
    expect(Segment::count())->toBe(1);

    // And the segment should have the same name as the caseload
    $segment = Segment::first();
    expect($segment->name)->toBe($caseload->name);

    // And the segment should have the same subject count as the caseload
    expect($segment->subjects->count())->toBe($caseloadSubjects->count());

    // And the segment should have the same campaign count as the caseload
    expect($segment->campaigns->count())->toBe($campaigns->count());
});
