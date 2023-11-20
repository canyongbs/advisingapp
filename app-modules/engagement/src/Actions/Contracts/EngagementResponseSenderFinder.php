<?php

namespace Assist\Engagement\Actions\Contracts;

use Assist\Prospect\Models\Prospect;
use Assist\AssistDataModel\Models\Student;

interface EngagementResponseSenderFinder
{
    public function find(string $phoneNumber): Student|Prospect|null;
}
