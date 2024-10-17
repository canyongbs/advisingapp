<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

use LastDragon_ru\LaraASP\GraphQL\Builder\Contracts\Operator;
use LastDragon_ru\LaraASP\GraphQL\SortBy\Definitions\SortByDirective;
use LastDragon_ru\LaraASP\GraphQL\Stream\Definitions\StreamDirective;
use LastDragon_ru\LaraASP\GraphQL\SearchBy\Definitions\SearchByDirective;

/**
 * -----------------------------------------------------------------------------
 * GraphQL Settings
 * -----------------------------------------------------------------------------
 *
 * Note: You need to clear/rebuild the cached schema and IDE helper files after change.
 *
 * @see https://lighthouse-php.com/master/api-reference/commands.html#clear-cache
 * @see https://lighthouse-php.com/master/api-reference/commands.html#ide-helper
 *
 * @var array{
 *      search_by: array{
 *          operators: array<string, list<string|class-string<Operator>>>,
 *      },
 *      sort_by: array{
 *          operators: array<string, list<string|class-string<Operator>>>,
 *      },
 *      stream: array{
 *          search: array{
 *              name: string,
 *              enabled: bool,
 *          },
 *          sort: array{
 *              name: string,
 *              enabled: bool,
 *          },
 *          limit: array{
 *              name: string,
 *              default: int<1, max>,
 *              max: int<1, max>,
 *          },
 *          offset: array{
 *              name: string,
 *          }
 *      }
 *      } $settings
 */
$settings = [
    /**
     * Settings for {@see SearchByDirective @searchBy} directive.
     */
    'search_by' => [
        /**
         * Operators
         * ---------------------------------------------------------------------
         *
         * You can redefine operators for exiting (=default) types OR define own
         * types here. Note that directives is the recommended way and have
         * priority over the array. Please see the documentation for more
         * details.
         *
         * @see ../README.md#type-operators
         */
        'operators' => [
            // empty
        ],
    ],

    /**
     * Settings for {@see SortByDirective @sortBy} directive.
     */
    'sort_by' => [
        /**
         * Operators
         * ---------------------------------------------------------------------
         *
         * You can redefine operators for exiting (=default) types OR define own
         * types here. Note that directives is the recommended way and have
         * priority over the array. Please see the documentation for more
         * details.
         *
         * @see ../README.md#operators-1
         */
        'operators' => [
            // empty
        ],
    ],

    /**
     * Settings for {@see StreamDirective @stream} directive.
     */
    'stream' => [
        'search' => [
            'name' => 'where',
            'enabled' => true,
        ],
        'sort' => [
            'name' => 'order',
            'enabled' => true,
        ],
        'limit' => [
            'name' => 'limit',
            'default' => 25,
            'max' => 100,
        ],
        'offset' => [
            'name' => 'offset',
        ],
    ],
];

return $settings;
