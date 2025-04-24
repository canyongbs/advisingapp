<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class GPT41MiniAnd41NanoFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
