<?php

namespace Assist\KnowledgeBase\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;

class KnowledgeBaseStatusSeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeBaseStatus::factory()
            ->count(3)
            ->sequence(
                [
                    'name' => 'Draft',
                ],
                [
                    'name' => 'Published',
                ],
                [
                    'name' => 'Archived',
                ],
            )
            ->create();
    }
}
