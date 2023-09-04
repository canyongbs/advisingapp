<?php

namespace App\Console\Commands;

use Sqids\Sqids;
use Illuminate\Console\Command;
use Symfony\Component\Uid\Ulid;

class SqidTest extends Command
{
    protected $signature = 'app:sqid-test';

    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sqids = new Sqids();
        $encode = $sqids->encode([time()]);
        $length = strlen($encode);
        $remainingLenth = 12 - $length;

        // 916,132,832 - 14,776,336 permutations per second
        $this->info('SQID timestamp with 12 chars SR-' . $encode . str()->random($remainingLenth));

        $sqids = new Sqids();
        $encode = $sqids->encode([now()->format('ymd')]);
        $length = strlen($encode);
        $remainingLenth = 12 - $length;

        // 56,800,235,584 - 916,132,832 permutations per day
        $this->info('SQID ymd with 12 chars SR-' . $encode . str()->random($remainingLenth));

        // Virtually unlimited permutations per second
        $this->info('ULID: SR-' . Ulid::generate());
    }
}
