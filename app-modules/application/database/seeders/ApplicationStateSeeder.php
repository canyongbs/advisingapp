<?php

namespace AdvisingApp\Application\Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\Application\Models\ApplicationState;
use AdvisingApp\Application\Enums\ApplicationStateColorOptions;
use AdvisingApp\Application\Enums\ApplicationStateClassification;

class ApplicationStateSeeder extends Seeder
{
    public function run(): void
    {
        ApplicationState::factory()
            ->createMany(
                [
                    [
                        'classification' => ApplicationStateClassification::Received,
                        'name' => 'Application Received',
                        'color' => ApplicationStateColorOptions::Info,
                        'description' => 'The college has received your application for admission.',
                    ],
                    [
                        'classification' => ApplicationStateClassification::Review,
                        'name' => 'Admission Review',
                        'color' => ApplicationStateColorOptions::Warning,
                        'description' => 'Your application is under review. Please submit any pending tasks to complete your application.',
                    ],
                    [
                        'classification' => ApplicationStateClassification::Complete,
                        'name' => 'Application Complete',
                        'color' => ApplicationStateColorOptions::Primary,
                        'description' => 'Your application is complete and under final review for admission.',
                    ],
                    [
                        'classification' => ApplicationStateClassification::DocumentsRequired,
                        'name' => 'Additional Documents Required',
                        'color' => ApplicationStateColorOptions::Warning,
                        'description' => 'After initial review, additional documents are needed to process your application. Please check your tasks for more information.',
                    ],
                    [
                        'classification' => ApplicationStateClassification::Admit,
                        'name' => 'Admit',
                        'color' => ApplicationStateColorOptions::Success,
                        'description' => 'A final decision has been made on your application.',
                    ],
                    [
                        'classification' => ApplicationStateClassification::Deny,
                        'name' => 'Deny',
                        'color' => ApplicationStateColorOptions::Danger,
                        'description' => 'A final decision has been made on your application.',
                    ],
                ]
            );
    }
}
