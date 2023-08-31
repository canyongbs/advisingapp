<?php

namespace Assist\IntegrationTwilio\Actions\Playground;

use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Actions\Contracts\EngagementResponseSenderFinder;

class FindEngagementResponseSender implements EngagementResponseSenderFinder
{
    public function find(string $phoneNumber): Student|Prospect|null
    {
        return Student::where('mobile', config('services.twilio.from_number'))
            ->first();
    }
}
