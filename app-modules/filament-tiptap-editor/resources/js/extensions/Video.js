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

export const Video = Node.create({
    name: 'video',

    selectable: true,

    draggable: true,

    atom: true,

    inline() {
        return this.options.inline;
    },

    group() {
        return this.options.inline ? 'inline' : 'block';
    },

    addOptions() {
        return {
            inline: false,
            HTMLAttributes: {
                autoplay: null,
                controls: null,
                loop: null,
            },
            width: 640,
            height: 480,
        };
    },

    addAttributes() {
        return {
            style: {
                default: null,
                parseHTML: (element) => element.getAttribute('style'),
            },
            responsive: {
                default: true,
                parseHTML: (element) => element.classList.contains('responsive') ?? false,
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
            autoplay: {
                default: null,
                parseHTML: (element) => element.getAttribute('autoplay'),
            },
            controls: {
                default: null,
                parseHTML: (element) => element.getAttribute('controls'),
            },
            loop: {
                default: null,
                parseHTML: (element) => element.getAttribute('loop'),
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
                tag: 'div[data-native-video] video',
            },
        ];
    },

    addCommands() {
        return {
            setVideo:
                (options) =>
                ({ commands }) => {
                    return commands.insertContent({
                        type: this.name,
                        attrs: options,
                    });
                },
        };
    },

    renderHTML({ HTMLAttributes }) {
        return [
            'div',
            { 'data-native-video': '', class: HTMLAttributes.responsive ? 'responsive' : null },
            [
                'video',
                {
                    src: HTMLAttributes.src,
                    width: HTMLAttributes.width,
                    height: HTMLAttributes.height,
                    autoplay: HTMLAttributes.autoplay ? 'true' : null,
                    controls: HTMLAttributes.controls ? 'true' : null,
                    loop: HTMLAttributes.loop ? 'true' : null,
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
