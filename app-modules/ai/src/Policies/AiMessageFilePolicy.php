<?php

namespace AdvisingApp\Ai\Policies;

use App\Models\Authenticatable;
use Illuminate\Auth\Access\Response;
use App\Concerns\PerformsLicenseChecks;
use AdvisingApp\Authorization\Enums\LicenseType;

class AiMessageFilePolicy
{
    use PerformsLicenseChecks;

    public function before(Authenticatable $authenticatable): ?Response
    {
        if (! is_null($response = $this->hasLicenses($authenticatable, LicenseType::ConversationalAi))) {
            return $response;
        }

        return null;
    }
}
