<?php

namespace Assist\KnowledgeBase\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;

class KnowledgeBaseCategorySeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeBaseCategory::factory()
            ->count(3)
            ->sequence(
                [
                    'name' => 'Category 1',
                ],
                [
                    'name' => 'Category 2',
                ],
                [
                    'name' => 'Category 3',
                ],
            )
            ->create();
    }
}
