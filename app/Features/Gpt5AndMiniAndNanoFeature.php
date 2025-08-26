<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class Gpt5AndMiniAndNanoFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
