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
import { mergeAttributes, Node } from '@tiptap/core';

import { getEmbedURLFromYoutubeURL, isValidYoutubeUrl } from './utils';

export const Youtube = Node.create({
    name: 'youtube',

    selectable: true,

    draggable: true,

    atom: true,

    addOptions() {
        return {
            inline: false,
            HTMLAttributes: {},
            width: 640,
            height: 480,
        };
    },

    inline() {
        return this.options.inline;
    },

    group() {
        return this.options.inline ? 'inline' : 'block';
    },

    addAttributes() {
        return {
            style: {
                default: null,
                parseHTML: (element) => element.getAttribute('style'),
            },
            src: {
                default: null,
            },
            width: {
                default: this.options.width,
                parseHTML: (element) => element.getAttribute('width'),
            },
            height: {
                default: this.options.height,
                parseHTML: (element) => element.getAttribute('height'),
            },
            responsive: {
                default: true,
                parseHTML: (element) => element.classList.contains('responsive') ?? false,
            },
            start: {
                default: 0,
            },
            controls: {
                default: true,
            },
            nocookie: {
                default: false,
            },
            'data-aspect-width': {
                default: null,
                parseHTML: (element) => element.getAttribute('data-aspect-width'),
            },
            'data-aspect-height': {
                default: null,
                parseHTML: (element) => element.getAttribute('data-aspect-height'),
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'div[data-youtube-video] iframe',
            },
        ];
    },

    addCommands() {
        return {
            setYoutubeVideo:
                (options) =>
                ({ commands }) => {
                    if (!isValidYoutubeUrl(options.src)) {
                        return false;
                    }

                    const embedUrl = getEmbedURLFromYoutubeURL({
                        url: options.src,
                        controls: options.controls,
                        nocookie: options.nocookie,
                        startAt: options.start || 0,
                    });

                    return commands.insertContent({
                        type: this.name,
                        attrs: {
                            ...options,
                            src: embedUrl,
                        },
                    });
                },
        };
    },

    renderHTML({ HTMLAttributes }) {
        const embedUrl = getEmbedURLFromYoutubeURL({
            url: HTMLAttributes.src,
            controls: HTMLAttributes.controls,
            nocookie: HTMLAttributes.nocookie,
            startAt: HTMLAttributes.start || 0,
        });

        return [
            'div',
            {
                'data-youtube-video': '',
                class: HTMLAttributes.responsive ? 'responsive' : null,
            },
            [
                'iframe',
                {
                    src: embedUrl,
                    width: HTMLAttributes.width,
                    height: HTMLAttributes.height,
                    allowfullscreen: this.options.allowFullscreen,
                    style: HTMLAttributes.responsive
                        ? `aspect-ratio: ${HTMLAttributes['data-aspect-width']} / ${HTMLAttributes['data-aspect-height']}; width: 100%; height: auto;`
                        : null,
                    'data-aspect-width': HTMLAttributes.responsive ? HTMLAttributes['data-aspect-width'] : null,
                    'data-aspect-height': HTMLAttributes.responsive ? HTMLAttributes['data-aspect-height'] : null,
                },
            ],
        ];
    },
});
