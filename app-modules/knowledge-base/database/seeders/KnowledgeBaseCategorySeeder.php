<?php

namespace Assist\KnowledgeBase\Database\Seeders;

use Illuminate\Database\Seeder;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;

class KnowledgeBaseCategorySeeder extends Seeder
{
    public function run(): void
    {
        KnowledgeBaseCategory::factory()
            ->createMany(
                [
                    ['name' => 'Admissions, Records, and Registration (AR&R)'],
                    ['name' => 'Advising'],
                    ['name' => 'Clock Hour Programs'],
                    ['name' => 'Enrollment Services'],
                    ['name' => 'Financial Aid'],
                    ['name' => 'International Education'],
                    ['name' => 'IT Help Desk'],
                    ['name' => 'Recruitment'],
                    ['name' => 'Special Populations'],
                    ['name' => 'Student Business Services'],
                    ['name' => 'Veterans Services'],
                ]
            );
    }
}
