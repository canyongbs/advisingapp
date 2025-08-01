<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ProjectComingSoonFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
