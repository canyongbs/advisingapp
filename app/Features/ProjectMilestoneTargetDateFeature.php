<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ProjectMilestoneTargetDateFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
