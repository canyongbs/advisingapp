<?php

namespace Assist\ServiceManagement\Tests\RequestFactories;

use App\Models\Institution;
use Worksome\RequestFactories\RequestFactory;
use Assist\ServiceManagement\Models\ServiceRequestType;
use Assist\ServiceManagement\Models\ServiceRequestStatus;
use Assist\ServiceManagement\Models\ServiceRequestPriority;

class EditCaseItemRequestFactory extends RequestFactory
{
    public function definition(): array
    {
        return [
            'institution_id' => Institution::factory()->create()->id,
            'status_id' => ServiceRequestStatus::factory()->create()->id,
            'priority_id' => ServiceRequestPriority::factory()->create()->id,
            'type_id' => ServiceRequestType::factory()->create()->id,
            'close_details' => $this->faker->sentence,
            'res_details' => $this->faker->sentence,
        ];
    }
}
