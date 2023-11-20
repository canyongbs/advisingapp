<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\InteractionOutcome;

class InteractionOutcomeSeeder extends Seeder
{
    public function run(): void
    {
        InteractionOutcome::factory()
            ->createMany(
                [
                    ['name' => 'Does Not Apply'],
                    ['name' => 'Live Contact'],
                    ['name' => 'Voicemail'],
                    ['name' => 'No Voicemail'],
                    ['name' => 'Dropped Call'],
                ]
            );
    }
}
