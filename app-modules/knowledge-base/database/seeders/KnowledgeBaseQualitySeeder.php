<?php

namespace Assist\KnowledgeBase\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;

class KnowledgeBaseQualitySeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeBaseQuality::factory()
            ->createMany(
                [
                    ['name' => 'Good'],
                    ['name' => 'Review Needed'],
                ]
            );
    }
}
