<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ProspectStatusFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
