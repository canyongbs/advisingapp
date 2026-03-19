<?php

namespace App\Features;

use App\Support\AbstractFeatureFlag;

class TenantConfigEncryptionFeature extends AbstractFeatureFlag
{
    public function resolve(mixed $scope): mixed
    {
        return false;
    }
}
