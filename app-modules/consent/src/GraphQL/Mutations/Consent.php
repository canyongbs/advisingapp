<?php

declare(strict_types = 1);

namespace AdvisingApp\Consent\GraphQL\Mutations;

use App\Models\User;
use Nuwave\Lighthouse\Execution\ResolveInfo;
use AdvisingApp\Consent\Models\ConsentAgreement;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final readonly class Consent
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ConsentAgreement
    {
        $consentAgreement = ConsentAgreement::find($args['id']);
        $user = User::find($args['user_id']);

        $user->consentTo($consentAgreement);

        return $consentAgreement;
    }
}
