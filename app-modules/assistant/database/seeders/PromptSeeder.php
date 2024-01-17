<?php

namespace AdvisingApp\Assistant\Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\Assistant\Models\Prompt;

class PromptSeeder extends Seeder
{
    public function run(): void
    {
        Prompt::factory()->count(20)->create();
    }
}
