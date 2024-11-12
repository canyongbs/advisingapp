<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class GenerateProspectFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
