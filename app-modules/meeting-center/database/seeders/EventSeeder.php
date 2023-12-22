<?php

namespace AdvisingApp\MeetingCenter\Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\MeetingCenter\Models\Event;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        Event::factory()
            ->count(20)
            ->create();
    }
}
