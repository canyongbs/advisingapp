<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class EmailTypeFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
