<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class ArchiveSubmissionsFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
