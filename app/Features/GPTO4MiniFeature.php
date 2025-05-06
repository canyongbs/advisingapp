<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class GPTO4MiniFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
