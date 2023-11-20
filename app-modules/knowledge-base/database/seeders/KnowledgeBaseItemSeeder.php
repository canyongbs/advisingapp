<?php

namespace Assist\KnowledgeBase\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;

class KnowledgeBaseItemSeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeBaseItem::factory()
            ->count(25)
            ->create();
    }
}
