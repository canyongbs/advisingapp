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

namespace FilamentTiptapEditor\Extensions\Nodes;

use Tiptap\Core\Node;
use Tiptap\Utils\HTML;

class Video extends Node
{
    public static $name = 'video';

    public function addOptions(): array
    {
        return [
            'inline' => false,
            'HTMLAttributes' => [
                'autoplay' => null,
                'controls' => null,
                'loop' => null,
            ],
            'allowFullscreen' => true,
            'width' => 640,
            'height' => 480,
        ];
    }

    public function addAttributes(): array
    {
        return [
            'responsive' => [
                'default' => true,
                'parseHTML' => function ($DOMNode) {
                    return str_contains($DOMNode->getAttribute('class'), 'responsive') ?? false;
                },
            ],
            'style' => [
                'default' => null,
                'parseHTML' => function ($DOMNode) {
                    return $DOMNode->firstChild->getAttribute('style');
                },
            ],
            'src' => [
                'default' => null,
                'parseHTML' => function ($DOMNode) {
                    return $DOMNode->firstChild->getAttribute('src');
                },
            ],
            'width' => [
                'default' => $this->options['width'],
                'parseHTML' => function ($DOMNode) {
                    return $DOMNode->firstChild->getAttribute('width');
                },
            ],
            'height' => [
                'default' => $this->options['height'],
                'parseHTML' => function ($DOMNode) {
                    return $DOMNode->firstChild->getAttribute('height');
                },
            ],
            'autoplay' => [
                'default' => null,
                'parseHTML' => function ($DOMNode) {
                    return $DOMNode->firstChild->getAttribute('autoplay');
                },
            ],
            'loop' => [
                'default' => null,
                'parseHTML' => function ($DOMNode) {
                    return $DOMNode->firstChild->getAttribute('loop');
                },
            ],
            'controls' => [
                'default' => null,
                'parseHTML' => function ($DOMNode) {
                    return $DOMNode->firstChild->getAttribute('controls');
                },
            ],
            'data-aspect-width' => [
                'default' => null,
                'parseHTML' => function ($DOMNode) {
                    return $DOMNode->firstChild->getAttribute('data-aspect-width');
                },
            ],
            'data-aspect-height' => [
                'default' => null,
                'parseHTML' => function ($DOMNode) {
                    return $DOMNode->firstChild->getAttribute('data-aspect-height');
                },
            ],
        ];
    }

    public function parseHTML(): array
    {
        return [
            [
                'tag' => 'div[data-native-video]',
            ],
        ];
    }

    public function renderHTML($node, $HTMLAttributes = []): array
    {
        return [
            'div',
            [
                'data-native-video' => true,
                'class' => $node->attrs->responsive ? 'responsive' : null,
            ],
            [
                'video',
                HTML::mergeAttributes($this->options['HTMLAttributes'], [
                    'src' => $node->attrs->src,
                    'width' => $node->attrs->width,
                    'height' => $node->attrs->height,
                    'autoplay' => $node->attrs->autoplay ? 'true' : null,
                    'loop' => $node->attrs->loop ? 'true' : null,
                    'controls' => $node->attrs->controls ? 'true' : null,
                    'style' => $node->attrs->style ?? null,
                ]),
            ],
        ];
    }
}
