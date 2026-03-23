<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ApplicationSubmissionStateFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
