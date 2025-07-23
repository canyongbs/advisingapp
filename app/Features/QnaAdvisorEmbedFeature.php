<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class QnaAdvisorEmbedFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
