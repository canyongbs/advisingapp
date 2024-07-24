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

namespace FilamentTiptapEditor\Extensions\Marks;

use Tiptap\Marks\Link as BaseLink;

class Link extends BaseLink
{
    public function addOptions(): array
    {
        return [
            'openOnClick' => true,
            'linkOnPaste' => true,
            'autoLink' => true,
            'protocols' => [],
            'HTMLAttributes' => [],
            'validate' => 'undefined',
        ];
    }

    public function addAttributes(): array
    {
        return [
            'href' => [
                'default' => null,
            ],
            'id' => [
                'default' => null,
            ],
            'target' => [
                'default' => $this->options['HTMLAttributes']['target'] ?? null,
            ],
            'hreflang' => [
                'default' => null,
            ],
            'rel' => [
                'default' => null,
            ],
            'referrerpolicy' => [
                'default' => null,
            ],
            'class' => [
                'default' => null,
            ],
            'as_button' => [
                'default' => null,
                'parseHTML' => function ($DOMNode) {
                    if ($DOMNode->getAttribute('as_button') === 'true') {
                        return true;
                    }

                    return $DOMNode->getAttribute('data-as-button') ?: null;
                },
                'renderHTML' => function ($attributes) {
                    if (! property_exists($attributes, 'as_button')) {
                        return null;
                    }

                    return [
                        'data-as-button' => $attributes->as_button ?? null,
                    ];
                },
            ],
            'button_theme' => [
                'default' => null,
                'parseHTML' => function ($DOMNode) {
                    if ($theme = $DOMNode->getAttribute('data-as-button-theme')) {
                        return $theme;
                    }

                    if ($theme = $DOMNode->getAttribute('button_theme')) {
                        return $theme;
                    }

                    return null;
                },
                'renderHTML' => function ($attributes) {
                    if (! property_exists($attributes, 'button_theme') || strlen($attributes->button_theme) === 0) {
                        return null;
                    }

                    return [
                        'data-as-button-theme' => $attributes->button_theme,
                    ];
                },
            ],
        ];
    }
}
