<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class O1MiniAndO3MiniFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
