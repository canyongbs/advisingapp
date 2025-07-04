<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class AssignmentsFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
