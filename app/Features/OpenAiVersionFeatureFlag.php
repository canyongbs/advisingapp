<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class OpenAiVersionFeatureFlag extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
