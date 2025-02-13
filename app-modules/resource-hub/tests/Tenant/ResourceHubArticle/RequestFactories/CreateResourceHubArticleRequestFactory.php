<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\ResourceHub\Tests\ResourceHubArticle\RequestFactories;

use AdvisingApp\Division\Models\Division;
use AdvisingApp\ResourceHub\Models\ResourceHubCategory;
use AdvisingApp\ResourceHub\Models\ResourceHubQuality;
use AdvisingApp\ResourceHub\Models\ResourceHubStatus;
use Worksome\RequestFactories\RequestFactory;

class CreateResourceHubArticleRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'title' => fake()->words(5, true),
            'public' => fake()->boolean(),
            'notes' => fake()->paragraph(),
            'quality_id' => ResourceHubQuality::inRandomOrder()->first()?->id ?? ResourceHubQuality::factory()->create()->id,
            'status_id' => ResourceHubStatus::inRandomOrder()->first()?->id ?? ResourceHubStatus::factory()->create()->id,
            'category_id' => ResourceHubCategory::inRandomOrder()->first()?->id ?? ResourceHubCategory::factory()->create()->id,
            'division' => [Division::inRandomOrder()->first()?->id ?? Division::factory()->create()->id],
        ];
    }
}
