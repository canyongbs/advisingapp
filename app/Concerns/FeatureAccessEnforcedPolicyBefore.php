<?php

namespace App\Concerns;

use App\Enums\Feature;
use Illuminate\Support\Facades\Gate;
use App\Support\FeatureAccessResponse;

trait FeatureAccessEnforcedPolicyBefore
{
    public function before(): FeatureAccessResponse | null | bool
    {
        return Gate::check(
            collect($this->requiredFeatures())->map(fn (Feature $feature) => $feature->value)
        )
            ? null
            : FeatureAccessResponse::deny();
    }

    /**
     * @return array<Feature>
     */
    abstract protected function requiredFeatures(): array;
}
