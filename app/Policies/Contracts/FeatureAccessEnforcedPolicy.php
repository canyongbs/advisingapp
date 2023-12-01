<?php

namespace App\Policies\Contracts;

use App\Support\FeatureAccessResponse;

interface FeatureAccessEnforcedPolicy
{
    public function before(): FeatureAccessResponse | null | bool;
}
