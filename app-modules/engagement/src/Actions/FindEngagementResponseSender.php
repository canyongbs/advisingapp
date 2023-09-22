<?php

namespace Assist\Engagement\Actions;

use Illuminate\Support\Facades\Log;
use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;
use Assist\Engagement\Actions\Contracts\EngagementResponseSenderFinder;

class FindEngagementResponseSender implements EngagementResponseSenderFinder
{
    public function find(string $phoneNumber): Student|Prospect|null
    {
        // Student currently takes priority, but determine if we potentially want to store this response
        // For *all* potential matches instead of just a singular result.
        // TODO: Make use of shared Student/Prospect implementation
        if (! is_null($student = Student::where('mobile', $phoneNumber)->orWhere('phone', $phoneNumber)->first())) {
            return $student;
        }

        if (! is_null($prospect = Prospect::where('mobile', $phoneNumber)->orWhere('phone', $phoneNumber)->first())) {
            return $prospect;
        }

        // TODO Perhaps send a notification to an admin, but don't need to throw an exception.
        Log::error("Could not find a Student or Prospect with the given phone number: {$phoneNumber}");

        return null;
    }
}
