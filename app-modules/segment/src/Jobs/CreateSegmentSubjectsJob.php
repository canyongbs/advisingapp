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

namespace AdvisingApp\Segment\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use AdvisingApp\Segment\Models\Segment;
use Illuminate\Queue\InteractsWithQueue;
use AdvisingApp\Segment\Enums\SegmentModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateSegmentSubjectsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $segment;

    protected $sisidChunk;

    protected $model;

    /**
     * Create a new job instance.
     */
    public function __construct(Segment $segment, array $sisidChunk, SegmentModel $model)
    {
        $this->segment = $segment;
        $this->sisidChunk = $sisidChunk;
        $this->model = $model;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $subjectData = collect($this->sisidChunk)->map(fn ($id) => [
            'subject_id' => $id,
            'subject_type' => $this->model,
        ])->toArray();
        $this->segment->subjects()->createMany($subjectData);
    }
}
