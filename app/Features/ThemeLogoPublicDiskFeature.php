<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ThemeLogoPublicDiskFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
