<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class InteractableTypeFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
