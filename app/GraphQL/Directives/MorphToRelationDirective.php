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

declare(strict_types = 1);

namespace App\GraphQL\Directives;

use function array_merge;

use GraphQL\Language\DirectiveLocation;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;

use function is_a;

use LastDragon_ru\LaraASP\Eloquent\ModelHelper;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\BuilderFieldResolver;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\Context;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\Handler;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\TypeProvider;
use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\TypeSource;
use LastDragon_ru\LaraASP\GraphQL\Builder\Exceptions\OperatorUnsupportedBuilder;
use LastDragon_ru\LaraASP\GraphQL\Builder\Field;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Definitions\SearchByOperatorConditionDirective;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Exceptions\OperatorInvalidArgumentValue;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Operators\Complex\RelationshipType;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Operators\Operator;
use Nuwave\Lighthouse\Execution\Arguments\Argument;
use Nuwave\Lighthouse\Execution\Arguments\ArgumentSet;
use Override;

use function reset;

class MorphToRelationDirective extends Operator
{
    public function __construct(
        protected readonly SearchByOperatorConditionDirective $field,
        BuilderFieldResolver $resolver,
    ) {
        parent::__construct($resolver);
    }

    #[Override]
    public static function getName(): string
    {
        return 'relation';
    }

    #[Override]
    public function isAvailable(TypeProvider $provider, TypeSource $source, Context $context): bool
    {
        return parent::isAvailable($provider, $source, $context)
            && $source->isObject();
    }

    #[Override]
    public function getFieldType(TypeProvider $provider, TypeSource $source, Context $context): ?string
    {
        return $provider->getType(RelationshipType::class, $source, $context);
    }

    public function getFieldDescription(): string
    {
        return 'Relationship condition.';
    }

    public function isBuilderSupported(string $builder): bool
    {
        return is_a($builder, EloquentBuilder::class, true);
    }

    #[Override]
    public function call(
        Handler $handler,
        object $builder,
        Field $field,
        Argument $argument,
        Context $context,
    ): object {
        // TODO: Update this directive to remove things that are not needed for MorphTo relations

        // Supported?
        if (! ($builder instanceof EloquentBuilder)) {
            throw new OperatorUnsupportedBuilder($this, $builder);
        }

        // ArgumentSet?
        if (! ($argument->value instanceof ArgumentSet)) {
            throw new OperatorInvalidArgumentValue($this, ArgumentSet::class, $argument->value);
        }

        // Conditions
        $relation = (new ModelHelper($builder))->getRelation($field->getName());
        $has = $argument->value->arguments['where'] ?? null;
        $hasCount = $argument->value->arguments['count'] ?? null;
        $notExists = (bool) ($argument->value->arguments['notExists']->value ?? false);

        // Build
        $alias = $relation->getRelationCountHash(false);
        $count = 1;
        $operator = '>=';

        if ($hasCount instanceof Argument) {
            $query = $builder->getQuery()->newQuery();
            $query = $this->field->call($handler, $query, new Field(), $hasCount, $context);
            $where = reset($query->wheres);
            $count = $where['value'] ?? $count;
            $operator = $where['operator'] ?? $operator;
        } elseif ($notExists) {
            $count = 1;
            $operator = '<';
        }

        $relationshipTypes = [];

        foreach ($argument->value->arguments['where'] as $item) {
            if ($item instanceof ArgumentSet) {
                foreach ($item->arguments as $key => $argument) {
                    $relationshipTypes[$key] = function (EloquentBuilder $builder) use ($context, $relation, $handler, $alias, $has) {
                        if ($has instanceof Argument && $has->value instanceof ArgumentSet) {
                            if ($alias === '' || $alias === $relation->getRelationCountHash(false)) {
                                $alias = $builder->getModel()->getTable();
                            }

                            $handler->handle($builder, new Field($alias), $has->value, $context);
                        }
                    };
                }
            }
        }

        // Build
        $this->build(
            $builder,
            $relationshipTypes,
            $field,
            $operator,
            $count,
        );

        // Return
        return $builder;
    }

    /**
     * @inheritDoc
     */
    #[Override]
    protected static function locations(): array
    {
        return array_merge(parent::locations(), [
            DirectiveLocation::FIELD_DEFINITION,
        ]);
    }

    protected function build(
        EloquentBuilder $builder,
        array $relationshipTypes,
        Field $field,
        string $operator,
        int $count,
    ): void {
        foreach ($relationshipTypes as $type => $closure) {
            $method = array_key_first($relationshipTypes) == $type ? 'whereHasMorph' : 'orWhereHasMorph';

            $builder->{$method}($field->getName(), $type, $closure, $operator, $count);
        }
    }
}
