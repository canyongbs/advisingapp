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
import { callOrReturn, getExtensionField, Node, mergeAttributes, findParentNode, findChildren } from '@tiptap/core';
import { TextSelection } from 'prosemirror-state';
import { createGrid } from './utils/createGrid';
import { GapCursor } from 'prosemirror-gapcursor';

export const Grid = Node.create({
    name: 'grid',

    group: 'block',

    defining: true,

    isolating: true,

    allowGapCursor: false,

    content: 'gridColumn+',

    gridRole: 'grid',

    addOptions() {
        return {
            HTMLAttributes: {
                class: 'filament-tiptap-grid',
            },
        };
    },

    addAttributes() {
        return {
            type: {
                default: 'responsive',
                parseHTML: (element) => element.getAttribute('type'),
            },
            cols: {
                default: 2,
                parseHTML: (element) => element.getAttribute('cols'),
            },
        };
    },

    parseHTML() {
        return [
            {
                tag: 'div',
                getAttrs: (node) => node.classList.contains('filament-tiptap-grid') && null,
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        return ['div', mergeAttributes(this.options.HTMLAttributes, HTMLAttributes), 0];
    },

    addCommands() {
        return {
            insertGrid:
                ({ cols = 3, type = 'responsive' } = {}) =>
                ({ tr, dispatch, editor }) => {
                    const node = createGrid(editor.schema, cols, type);

                    if (dispatch) {
                        const offset = tr.selection.anchor + 1;

                        tr.replaceSelectionWith(node)
                            .scrollIntoView()
                            .setSelection(TextSelection.near(tr.doc.resolve(offset)));
                    }

                    return true;
                },
        };
    },

    addKeyboardShortcuts() {
        return {
            'Mod-Alt-G': () => this.editor.commands.insertGrid(),
        };
    },

    extendNodeSchema(extension) {
        const context = {
            name: extension.name,
            options: extension.options,
            storage: extension.storage,
        };

        return {
            gridRole: callOrReturn(getExtensionField(extension, 'gridRole', context)),
        };
    },
});
