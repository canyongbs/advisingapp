<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class CustomerAdvisorResourceHubArticleAccessFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
