<?php

namespace App\Models\Concerns;

use Illuminate\Auth\Access\Response;

trait CanOrElse
{
    // For now, this is assuming the operator is 'or'
    // Though we may want to provide support for 'and' also
    public function canOrElse(iterable|string $abilities, string $denyResponse, mixed $arguments = []): Response
    {
        $abilities = is_iterable($abilities) ? $abilities : [$abilities];

        foreach ($abilities as $ability) {
            if ($this->can($ability, $arguments)) {
                return Response::allow();
            }
        }

        return Response::deny($denyResponse);
    }
}
