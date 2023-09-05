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
        // Plain Timestamp and 5 random Chars
        // Given 10 created per hour ~18 days of work are needed in order to have a 1% probability of at least one collision.
        $this->info('Plain Timestamp and 5 random Chars SR-' . now()->format('mY') . str()->random(5));

        // Plain Timestamp and 6 random Chars
        // Given 10 created per hour ~5 months of work are needed in order to have a 1% probability of at least one collision.
        $this->info('Plain Timestamp and 6 random Chars SR-' . now()->format('mY') . str()->random(6));

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

        $sqids = new Sqids(
            alphabet: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
        );
        $encode = $sqids->encode([time()]);
        $length = strlen($encode);
        $remainingLenth = 14 - $length;

        $this->info('SQID timestamp with 13 chars SR-' . $encode . $this->generateRandomString($remainingLenth));
    }

    public function generateRandomString($length)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, strlen($characters) - 1)];
        }

        return $randomString;
    }
}
