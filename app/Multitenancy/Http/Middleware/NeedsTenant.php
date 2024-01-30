<?php

namespace App\Multitenancy\Http\Middleware;

use Spatie\Multitenancy\Exceptions\NoCurrentTenant;

class NeedsTenant extends \Spatie\Multitenancy\Http\Middleware\NeedsTenant
{
    public function handleInvalidRequest()
    {
        report(NoCurrentTenant::make());

        return redirect(config('app.landlord_url'));
    }
}
