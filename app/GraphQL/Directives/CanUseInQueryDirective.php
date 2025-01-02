<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace App\GraphQL\Directives;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Nuwave\Lighthouse\Schema\Directives\BaseDirective;
use RuntimeException;

class CanUseInQueryDirective extends BaseDirective
{
    public static function definition(): string
    {
        return /** @lang GraphQL */ <<<'GRAPHQL'
"""
Check a Laravel Policy to ensure the current user is authorized to use this field in a query.
"""
directive @canUseInQuery(
  """
  The ability to check permissions for.
  """
  ability: String!
) repeatable on FIELD_DEFINITION | INPUT_FIELD_DEFINITION
GRAPHQL;
    }

    public function authorize(object $builder): void
    {
        if (! $builder instanceof Builder) {
            throw new RuntimeException('Query must be instance of [' . Builder::class . '] in order to extract the model for [@canUseInQuery].');
        }

        $arguments = $this->directiveNode->arguments;

        if (! $arguments->offsetExists(0)) {
            throw new RuntimeException('[@canUseInQuery] directive must have at least one ability to check.');
        }

        Gate::authorize($arguments->offsetGet(0)->value->value, $builder->getModel());
    }
}
