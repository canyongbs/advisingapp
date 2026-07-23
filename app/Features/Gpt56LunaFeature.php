<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class Gpt56LunaFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
