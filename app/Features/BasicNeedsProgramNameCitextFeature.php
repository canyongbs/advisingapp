<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class BasicNeedsProgramNameCitextFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
