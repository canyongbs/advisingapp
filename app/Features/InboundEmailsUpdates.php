<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class InboundEmailsUpdates extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
