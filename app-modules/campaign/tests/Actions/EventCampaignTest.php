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
use AdvisingApp\Campaign\Models\Campaign;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\Segment\Enums\SegmentType;

use function PHPUnit\Framework\assertTrue;

use AdvisingApp\MeetingCenter\Models\Event;

use function PHPUnit\Framework\assertCount;

use Illuminate\Database\Eloquent\Collection;
use AdvisingApp\Campaign\Models\CampaignAction;
use AdvisingApp\StudentDataModel\Models\Student;
use AdvisingApp\Campaign\Enums\CampaignActionType;
use Illuminate\Support\Facades\Event as FakeEvent;
use Spatie\LaravelSettings\Events\LoadingSettings;
use AdvisingApp\StudentDataModel\Models\Contracts\Educatable;

it('will create the event records for segment', function (Collection $educatables) {
    $segment = Segment::factory()->create([
        'type' => SegmentType::Static,
    ]);

    $educatables->each(function (Educatable $prospect) use ($segment) {
        $segment->subjects()->create([
            'subject_id' => $prospect->getKey(),
            'subject_type' => $prospect->getMorphClass(),
        ]);
    });

    $campaign = Campaign::factory()->create([
        'segment_id' => $segment->id,
    ]);

    $event = Event::factory()->create();

    $action = CampaignAction::factory()
        ->for($campaign, 'campaign')
        ->create([
            'type' => CampaignActionType::Event,
            'data' => [
                'event' => $event->id,
            ],
        ]);

    FakeEvent::fake()->except([
        LoadingSettings::class,
    ]);

    $action->execute();

    assertCount(3, $segment->subjects); // Check if 3 subjects were created for the segment
    assertTrue($campaign->hasBeenExecuted());
})->with([
    'prospects' => [
        'educatables' => fn () => Prospect::factory()->count(3)->create(),
    ],
    'students' => [
        'educatables' => fn () => Student::factory()->count(3)->create(),
    ],
]);
