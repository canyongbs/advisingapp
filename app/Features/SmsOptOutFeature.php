<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class SmsOptOutFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
