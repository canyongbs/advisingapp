<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class PastSubmissionsFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
