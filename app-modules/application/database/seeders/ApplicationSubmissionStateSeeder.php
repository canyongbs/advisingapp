<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Application\Database\Seeders;

use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateColorOptions;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use Illuminate\Database\Seeder;

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
