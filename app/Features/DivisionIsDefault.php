<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class DivisionIsDefault extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
