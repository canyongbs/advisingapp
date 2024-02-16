<?php

declare(strict_types = 1);

namespace AdvisingApp\Consent\GraphQL\Mutations;

use Nuwave\Lighthouse\Execution\ResolveInfo;
use AdvisingApp\Consent\Models\ConsentAgreement;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

final readonly class Revoke
{
    public function __invoke(mixed $root, array $args, GraphQLContext $context, ResolveInfo $resolveInfo): ConsentAgreement
    {
        $consentAgreement = ConsentAgreement::find($args['id']);

        $consentAgreement->userConsentAgreements()
            ->whereRelation('user', 'id', $args['user_id'])
            ->delete();

        return $consentAgreement;
    }
}
