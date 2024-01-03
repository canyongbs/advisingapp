<?php

namespace AdvisingApp\Analytics\Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\Analytics\Models\AnalyticsResourceCategory;
use AdvisingApp\Analytics\Enums\AnalyticsResourceCategoryClassification;

class AnalyticsResourceCategorySeeder extends Seeder
{
    public function run(): void
    {
        AnalyticsResourceCategory::factory()
            ->createMany([
                [
                    'name' => 'Public',
                    'classification' => AnalyticsResourceCategoryClassification::Public,
                ],
                [
                    'name' => 'Internal',
                    'classification' => AnalyticsResourceCategoryClassification::Internal,
                ],
                [
                    'name' => 'Restricted Internal',
                    'classification' => AnalyticsResourceCategoryClassification::RestrictedInternal,
                ],
            ]);
    }
}
