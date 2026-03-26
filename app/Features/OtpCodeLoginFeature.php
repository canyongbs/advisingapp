<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class OtpCodeLoginFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
