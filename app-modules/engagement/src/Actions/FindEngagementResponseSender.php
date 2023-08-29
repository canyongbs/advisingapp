<?php

namespace Assist\Engagement\Actions;

use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Exceptions\UnknownEngagementSenderException;

class FindEngagementResponseSender
{
    public function __invoke(string $phoneNumber): Student|Prospect
    {
        // Student currently takes priority, but determine if we potentially want to store this response
        // For *all* potential matches instead of just a singular result.
        if (! is_null($student = Student::where('mobile', $phoneNumber)->orWhere('phone', $phoneNumber)->first())) {
            return $student;
        }

        if (! is_null($prospect = Prospect::where('mobile', $phoneNumber)->orWhere('phone', $phoneNumber)->first())) {
            return $prospect;
        }

        throw new UnknownEngagementSenderException("Could not find a Student or Prospect with the given phone number: {$phoneNumber}");
    }
}
