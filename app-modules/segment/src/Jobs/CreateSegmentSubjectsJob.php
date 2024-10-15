<?php

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
