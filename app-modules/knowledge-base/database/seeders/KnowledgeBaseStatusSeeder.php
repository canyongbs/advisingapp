<?php

namespace Assist\KnowledgeBase\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;

class KnowledgeBaseStatusSeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeBaseStatus::factory()
            ->createMany(
                [
                    ['name' => 'Draft'],
                    ['name' => 'In-Review'],
                    ['name' => 'Published'],
                    ['name' => 'Archived'],
                ]
            );
    }
}
