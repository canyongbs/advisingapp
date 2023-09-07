<?php

namespace Assist\Interaction\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\Interaction\Models\Interaction;

class InteractionSeeder extends Seeder
{
    public function run(): void
    {
        Interaction::factory()
            ->count(10)
            ->create();
    }

    public static function supplementalSeeders(): array
    {
        return [
            InteractionCampaignSeeder::class,
            InteractionDriverSeeder::class,
            InteractionOutcomeSeeder::class,
            InteractionStatusSeeder::class,
            InteractionTypeSeeder::class,
        ];
    }
}
