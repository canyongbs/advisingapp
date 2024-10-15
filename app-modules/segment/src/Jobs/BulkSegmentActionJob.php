<?php

namespace AdvisingApp\Segment\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use AdvisingApp\Segment\Models\Segment;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Database\Eloquent\Collection;

class BulkSegmentActionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    protected $records;

    protected $data;

    protected $context;

    protected $user;

    protected $segment;

    /**
     * Create a new job instance.
     */
    public function __construct(Collection $records, array $data, string $context, User $user, Segment $segment)
    {
        $this->records = $records;
        $this->data = $data;
        $this->context = $context;
        $this->user = $user;
        $this->segment = $segment;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $segment = $this->segment;
        $id = $this->context == 'students' ? 'sisid' : 'id';
        $this->records->pluck($id)->chunk(100)->each(function ($sisidChunk) use ($segment) {
            CreateSegmentSubjectsJob::dispatch($segment, $sisidChunk->toArray(), $this->data['model']);
        });
    }
}
