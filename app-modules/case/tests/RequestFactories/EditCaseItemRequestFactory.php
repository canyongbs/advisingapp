<?php

namespace Assist\Case\Tests\RequestFactories;

use App\Models\Institution;
use Assist\Case\Models\CaseItemType;
use Assist\Case\Models\CaseItemStatus;
use Assist\Case\Models\CaseItemPriority;
use Worksome\RequestFactories\RequestFactory;

class EditCaseItemRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'institution' => Institution::factory()->create()->id,
            'status' => CaseItemStatus::factory()->create()->id,
            'priority' => CaseItemPriority::factory()->create()->id,
            'type' => CaseItemType::factory()->create()->id,
            'close_details' => $this->faker->sentence,
            'res_details' => $this->faker->sentence,
        ];
    }
}
