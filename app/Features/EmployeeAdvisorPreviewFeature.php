<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class EmployeeAdvisorPreviewFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
