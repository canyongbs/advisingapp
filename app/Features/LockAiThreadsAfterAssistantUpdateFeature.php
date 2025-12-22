<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class LockAiThreadsAfterAssistantUpdateFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
