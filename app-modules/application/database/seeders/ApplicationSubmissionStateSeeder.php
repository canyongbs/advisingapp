<?php

namespace AdvisingApp\Application\Database\Seeders;

use Illuminate\Database\Seeder;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateColorOptions;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;

class ApplicationSubmissionStateSeeder extends Seeder
{
    public function run(): void
    {
        ApplicationSubmissionState::factory()
            ->createMany(
                [
                    [
                        'classification' => ApplicationSubmissionStateClassification::Received,
                        'name' => 'Application Received',
                        'color' => ApplicationSubmissionStateColorOptions::Info,
                        'description' => 'The college has received your application for admission.',
                    ],
                    [
                        'classification' => ApplicationSubmissionStateClassification::Review,
                        'name' => 'Admission Review',
                        'color' => ApplicationSubmissionStateColorOptions::Warning,
                        'description' => 'Your application is under review. Please submit any pending tasks to complete your application.',
                    ],
                    [
                        'classification' => ApplicationSubmissionStateClassification::Complete,
                        'name' => 'Application Complete',
                        'color' => ApplicationSubmissionStateColorOptions::Primary,
                        'description' => 'Your application is complete and under final review for admission.',
                    ],
                    [
                        'classification' => ApplicationSubmissionStateClassification::DocumentsRequired,
                        'name' => 'Additional Documents Required',
                        'color' => ApplicationSubmissionStateColorOptions::Warning,
                        'description' => 'After initial review, additional documents are needed to process your application. Please check your tasks for more information.',
                    ],
                    [
                        'classification' => ApplicationSubmissionStateClassification::Admit,
                        'name' => 'Admit',
                        'color' => ApplicationSubmissionStateColorOptions::Success,
                        'description' => 'A final decision has been made on your application.',
                    ],
                    [
                        'classification' => ApplicationSubmissionStateClassification::Deny,
                        'name' => 'Deny',
                        'color' => ApplicationSubmissionStateColorOptions::Danger,
                        'description' => 'A final decision has been made on your application.',
                    ],
                ]
            );
    }
}
