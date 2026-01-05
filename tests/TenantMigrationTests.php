<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

// Add tests for migration files here

// use Illuminate\Support\Facades\Artisan;
// use Symfony\Component\Console\Command\Command;

// Example migration test, leave commented out for future use as a template/example
//describe('2025_01_01_165527_tmp_data_do_a_thing', function () {
//    it('properly changed the data', function () {
//        isolatedMigration(
//            '2025_01_01_165527_tmp_data_do_a_thing',
//            function () {
//                // Setup data before migration
//
//                // Run the migration
//                $migrate = Artisan::call('migrate', ['--path' => 'app/database/migrations/2025_01_01_165527_tmp_data_do_a_thing.php']);
//                // Confirm migration ran successfully
//                expect($migrate)->toBe(Command::SUCCESS);
//
//                // Add any assertions to verify the migration's effects
//            }
//        );
//    });
//});

use AdvisingApp\Interaction\Models\Interaction;
use AdvisingApp\Interaction\Models\InteractionDriver;
use AdvisingApp\Interaction\Models\InteractionInitiative;
use AdvisingApp\Interaction\Models\InteractionOutcome;
use AdvisingApp\Interaction\Models\InteractionRelation;
use AdvisingApp\Interaction\Models\InteractionStatus;
use AdvisingApp\Interaction\Models\InteractionType;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;

describe('2025_12_24_002713_data_add_interactable_type_to_interactions', function () {
    it('properly duplicates existing meta interaction model records', function () {
        isolatedMigration(
            '2025_12_24_002713_data_add_interactable_type_to_interactions',
            function () {
                //Setup data
                $interactionDriver = InteractionDriver::factory()->create();
                $interactionInitiative = InteractionInitiative::factory()->create();
                $interactionOutcome = InteractionOutcome::factory()->create();
                $interactionRelation = InteractionRelation::factory()->create();
                $interactionStatus = InteractionStatus::factory()->create();
                $interactionType = InteractionType::factory()->create();

                expect(InteractionDriver::count())->toBe(1);
                expect(InteractionInitiative::count())->toBe(1);
                expect(InteractionOutcome::count())->toBe(1);
                expect(InteractionRelation::count())->toBe(1);
                expect(InteractionStatus::count())->toBe(1);
                expect(InteractionType::count())->toBe(1);

                $student = Student::factory()->create();
                $prospect = Prospect::factory()->create();

                $studentInteraction = Interaction::factory()->for($student, 'interactable')->create([
                    'interaction_driver_id' => $interactionDriver,
                    'interaction_initiative_id' => $interactionInitiative,
                    'interaction_outcome_id' => $interactionOutcome,
                    'interaction_relation_id' => $interactionRelation,
                    'interaction_status_id' => $interactionStatus,
                    'interaction_type_id' => $interactionType,
                ]);

                $prospectInteraction = Interaction::factory()->for($prospect, 'interactable')->create([
                    'interaction_driver_id' => $interactionDriver,
                    'interaction_initiative_id' => $interactionInitiative,
                    'interaction_outcome_id' => $interactionOutcome,
                    'interaction_relation_id' => $interactionRelation,
                    'interaction_status_id' => $interactionStatus,
                    'interaction_type_id' => $interactionType,
                ]);

                //Run the migration
                $migrate = Artisan::call('migrate', ['--path' => 'app-modules/interaction/database/migrations/2025_12_24_002713_data_add_interactable_type_to_interactions.php']);

                //Confirm migration successful
                expect($migrate)->toBe(Command::SUCCESS);

                //Assert records duplicated
                expect(InteractionDriver::count())->toBe(2);
                expect(InteractionInitiative::count())->toBe(2);
                expect(InteractionOutcome::count())->toBe(2);
                expect(InteractionRelation::count())->toBe(2);
                expect(InteractionStatus::count())->toBe(2);
                expect(InteractionType::count())->toBe(2);

                //Assert originals are student records
                expect($interactionDriver->interactable_type)->toBe('student');
                expect($interactionInitiative->interactable_type)->toBe('student');
                expect($interactionOutcome->interactable_type)->toBe('student');
                expect($interactionRelation->interactable_type)->toBe('student');
                expect($interactionStatus->interactable_type)->toBe('student');
                expect($interactionType->interactable_type)->toBe('student');

                //Assert duplicates are prospect records
                expect(InteractionDriver::where('id', '!=', $interactionDriver->id)->first()->interactable_type)->toBe('prospect');
                expect(InteractionInitiative::where('id', '!=', $interactionInitiative->id)->first()->interactable_type)->toBe('prospect');
                expect(InteractionOutcome::where('id', '!=', $interactionOutcome->id)->first()->interactable_type)->toBe('prospect');
                expect(InteractionRelation::where('id', '!=', $interactionRelation->id)->first()->interactable_type)->toBe('prospect');
                expect(InteractionStatus::where('id', '!=', $interactionStatus->id)->first()->interactable_type)->toBe('prospect');
                expect(InteractionType::where('id', '!=', $interactionType->id)->first()->interactable_type)->toBe('prospect');

                //Assert interactions now reference correct models
                expect($studentInteraction->interaction_driver_id)->toBe($interactionDriver->id);
                expect($studentInteraction->interaction_initiative_id)->toBe($interactionInitiative->id);
                expect($studentInteraction->interaction_outcome_id)->toBe($interactionOutcome->id);
                expect($studentInteraction->interaction_relation_id)->toBe($interactionRelation->id);
                expect($studentInteraction->interaction_status_id)->toBe($interactionStatus->id);
                expect($studentInteraction->interaction_type_id)->toBe($interactionType->id);

                expect($prospectInteraction->interaction_driver_id)->not()->toBe($interactionDriver->id);
                expect($prospectInteraction->interaction_initiative_id)->not()->toBe($interactionInitiative->id);
                expect($prospectInteraction->interaction_outcome_id)->not()->toBe($interactionOutcome->id);
                expect($prospectInteraction->interaction_relation_id)->not()->toBe($interactionRelation->id);
                expect($prospectInteraction->interaction_status_id)->not()->toBe($interactionStatus->id);
                expect($prospectInteraction->interaction_type_id)->not()->toBe($interactionType->id);
            }
        );
    });
});
