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

    public static function metadataSeeders(): array
    {
        return [
            InteractionCampaignSeeder::class,
            InteractionDriverSeeder::class,
            InteractionInstitutionSeeder::class,
            InteractionOutcomeSeeder::class,
            InteractionRelationSeeder::class,
            InteractionStatusSeeder::class,
            InteractionTypeSeeder::class,
        ];
    }
}
