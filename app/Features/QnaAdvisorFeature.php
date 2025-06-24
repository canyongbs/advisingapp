<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class QnaAdvisorFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
