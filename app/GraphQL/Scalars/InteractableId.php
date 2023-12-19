<?php

declare(strict_types = 1);

namespace App\GraphQL\Scalars;

use GraphQL\Type\Definition\StringType;

/** Read more about scalars here: https://webonyx.github.io/graphql-php/type-definitions/scalars. */
final class InteractableId extends StringType
{
    public ?string $description = 'The `InteractableId` scalar type represents a unique identifier of an interactable entity. Due to the differences between some of the educatable entities, the type of the identifier may vary. A Prospect has a UUID, a Student has a sisid, which can be an integer, string, or UUID, and a Service Request has a UUID.';
}
