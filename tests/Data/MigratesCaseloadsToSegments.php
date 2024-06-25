<?php

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
