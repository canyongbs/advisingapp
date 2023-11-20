<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Interaction\Database\Factories;

use App\Models\User;
use Assist\Division\Models\Division;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Interaction\Models\Interaction;
use Assist\Interaction\Models\InteractionType;
use Assist\Interaction\Models\InteractionDriver;
use Assist\Interaction\Models\InteractionStatus;
use Assist\Interaction\Models\InteractionOutcome;
use Assist\Interaction\Models\InteractionCampaign;
use Assist\Interaction\Models\InteractionRelation;
use Assist\ServiceManagement\Models\ServiceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Interaction>
 */
class InteractionFactory extends Factory
{
    public function definition(): array
    {
        $interactable = fake()->randomElement([
            Student::class,
            Prospect::class,
            ServiceRequest::class,
        ]);

        $interactable = $interactable::factory()->create();

        return [
            'user_id' => User::factory(),
            'interactable_id' => $interactable->identifier(),
            'interactable_type' => $interactable->getMorphClass(),
            'interaction_type_id' => InteractionType::factory(),
            'interaction_relation_id' => InteractionRelation::factory(),
            'interaction_campaign_id' => InteractionCampaign::factory(),
            'interaction_driver_id' => InteractionDriver::factory(),
            'interaction_status_id' => InteractionStatus::factory(),
            'interaction_outcome_id' => InteractionOutcome::factory(),
            'division_id' => Division::factory(),
            'start_datetime' => now(),
            'end_datetime' => now()->addMinutes(5),
            'subject' => fake()->sentence(),
            'description' => fake()->paragraph(),
        ];
    }
}
