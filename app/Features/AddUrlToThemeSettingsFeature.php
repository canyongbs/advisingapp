<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class AddUrlToThemeSettingsFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
