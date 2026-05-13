<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class RenameQnaAdvisorsFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
