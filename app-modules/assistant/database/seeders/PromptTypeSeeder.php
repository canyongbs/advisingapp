<?php

namespace AdvisingApp\Assistant\Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\Assistant\Models\PromptType;

class PromptTypeSeeder extends Seeder
{
    public function run(): void
    {
        PromptType::factory()->count(20)->create();
    }
}
