<?php

namespace AdvisingApp\Prospect\Listeners;

use Nuwave\Lighthouse\Events\RegisterDirectiveNamespaces;

class RegisterGraphQLDirectives
{
    public function handle(RegisterDirectiveNamespaces $event): string
    {
        return 'AdvisingApp\\Prospect\\GraphQL\\Directives';
    }
}
