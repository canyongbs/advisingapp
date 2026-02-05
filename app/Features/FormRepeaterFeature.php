<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class FormRepeaterFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
