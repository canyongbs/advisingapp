<?php

namespace AdvisingApp\Analytics\Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\Analytics\Models\AnalyticsResourceSource;

class AnalyticsResourceSourceSeeder extends Seeder
{
    public function run(): void
    {
        AnalyticsResourceSource::factory()
            ->createMany([
                ['name' => 'Power BI'],
                ['name' => 'Tableau'],
                ['name' => 'Spreadsheet'],
            ]);
    }
}
