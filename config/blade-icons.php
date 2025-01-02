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

return [
    /*
    |--------------------------------------------------------------------------
    | Icons Sets
    |--------------------------------------------------------------------------
    |
    | With this config option you can define a couple of
    | default icon sets. Provide a key name for your icon
    | set and a combination from the options below.
    |
    */

    'sets' => [
        'default' => [
            /*
             |-----------------------------------------------------------------
             | Icons Path
             |-----------------------------------------------------------------
             |
             | Provide the relative path from your app root to your SVG icons
             | directory. Icons are loaded recursively so there's no need to
             | list every sub-directory.
             |
             | Relative to the disk root when the disk option is set.
             |
             */

            'path' => 'resources/svg',

            /*
             |-----------------------------------------------------------------
             | Filesystem Disk
             |-----------------------------------------------------------------
             |
             | Optionally, provide a specific filesystem disk to read
             | icons from. When defining a disk, the "path" option
             | starts relatively from the disk root.
             |
             */

            'disk' => '',

            /*
             |-----------------------------------------------------------------
             | Default Prefix
             |-----------------------------------------------------------------
             |
             | This config option allows you to define a default prefix for
             | your icons. The dash separator will be applied automatically
             | to every icon name. It's required and needs to be unique.
             |
             */

            'prefix' => 'icon',

            /*
             |-----------------------------------------------------------------
             | Fallback Icon
             |-----------------------------------------------------------------
             |
             | This config option allows you to define a fallback
             | icon when an icon in this set cannot be found.
             |
             */

            'fallback' => '',

            /*
             |-----------------------------------------------------------------
             | Default Set Classes
             |-----------------------------------------------------------------
             |
             | This config option allows you to define some classes which
             | will be applied by default to all icons within this set.
             |
             */

            'class' => '',

            /*
             |-----------------------------------------------------------------
             | Default Set Attributes
             |-----------------------------------------------------------------
             |
             | This config option allows you to define some attributes which
             | will be applied by default to all icons within this set.
             |
             */

            'attributes' => [
                // 'width' => 50,
                // 'height' => 50,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Default Classes
    |--------------------------------------------------------------------------
    |
    | This config option allows you to define some classes which
    | will be applied by default to all icons.
    |
    */

    'class' => '',

    /*
    |--------------------------------------------------------------------------
    | Global Default Attributes
    |--------------------------------------------------------------------------
    |
    | This config option allows you to define some attributes which
    | will be applied by default to all icons.
    |
    */

    'attributes' => [
        // 'width' => 50,
        // 'height' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Global Fallback Icon
    |--------------------------------------------------------------------------
    |
    | This config option allows you to define a global fallback
    | icon when an icon in any set cannot be found. It can
    | reference any icon from any configured set.
    |
    */

    'fallback' => '',

    /*
    |--------------------------------------------------------------------------
    | Components
    |--------------------------------------------------------------------------
    |
    | These config options allow you to define some
    | settings related to Blade Components.
    |
    */

    'components' => [
        /*
        |----------------------------------------------------------------------
        | Disable Components
        |----------------------------------------------------------------------
        |
        | This config option allows you to disable Blade components
        | completely. It's useful to avoid performance problems
        | when working with large icon libraries.
        |
        */

        'disabled' => false,

        /*
        |----------------------------------------------------------------------
        | Default Icon Component Name
        |----------------------------------------------------------------------
        |
        | This config option allows you to define the name
        | for the default Icon class component.
        |
        */

        'default' => 'icon',
    ],
];
