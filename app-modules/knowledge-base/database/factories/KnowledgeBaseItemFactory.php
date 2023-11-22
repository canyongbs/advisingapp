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

namespace Assist\KnowledgeBase\Database\Factories;

use Assist\Division\Models\Division;
use Assist\KnowledgeBase\Models\KnowledgeBaseItem;
use Illuminate\Database\Eloquent\Factories\Factory;
use Assist\KnowledgeBase\Models\KnowledgeBaseStatus;
use Assist\KnowledgeBase\Models\KnowledgeBaseQuality;
use Assist\KnowledgeBase\Models\KnowledgeBaseCategory;

/**
 * @extends Factory<KnowledgeBaseItem>
 */
class KnowledgeBaseItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'question' => $this->faker->sentence(),
            'public' => $this->faker->boolean(),
            'solution' => $this->faker->paragraph(),
            'notes' => $this->faker->paragraph(),
            'quality_id' => KnowledgeBaseQuality::inRandomOrder()->first() ?? KnowledgeBaseQuality::factory(),
            'status_id' => KnowledgeBaseStatus::inRandomOrder()->first() ?? KnowledgeBaseStatus::factory(),
            'category_id' => KnowledgeBaseCategory::inRandomOrder()->first() ?? KnowledgeBaseCategory::factory(),
        ];
    }

    public function configure(): static
    {
        return $this->afterMaking(function (KnowledgeBaseItem $knowledgeBaseItem) {
            // ...
        })->afterCreating(function (KnowledgeBaseItem $knowledgeBaseItem) {
            if ($knowledgeBaseItem->division->isEmpty()) {
                $knowledgeBaseItem->division()->attach(Division::first()?->id ?? Division::factory()->create()->id);
                $knowledgeBaseItem->save();
            }
        });
    }
}
