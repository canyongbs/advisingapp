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
import { Node, mergeAttributes, findParentNode, defaultBlockAt } from '@tiptap/core';
import { Selection } from 'prosemirror-state';

export const DetailsContent = Node.create({
    name: 'detailsContent',

    content: 'block+',

    defining: true,

    selectable: false,

    addOptions() {
        return {
            HTMLAttributes: {},
        };
    },

    parseHTML() {
        return [
            {
                tag: `div[data-type="details-content"]`,
            },
        ];
    },

    renderHTML({ HTMLAttributes }) {
        return [
            'div',
            mergeAttributes(this.options.HTMLAttributes, HTMLAttributes, { 'data-type': 'details-content' }),
            0,
        ];
    },

    addKeyboardShortcuts() {
        return {
            // allows double enter to exit content node
            Enter: ({ editor }) => {
                const { state, view } = editor;
                const { selection } = state;
                const { $from, empty } = selection;
                const detailsContent = findParentNode((node) => node.type === this.type)(selection);

                if (!empty || !detailsContent || !detailsContent.node.childCount) {
                    return false;
                }

                const fromIndex = $from.index(detailsContent.depth);
                const { childCount } = detailsContent.node;
                const isAtEnd = childCount === fromIndex + 1;

                if (!isAtEnd) {
                    return false;
                }

                const defaultChildType = detailsContent.node.type.contentMatch.defaultType;
                const defaultChildNode =
                    defaultChildType === null || defaultChildType === void 0
                        ? void 0
                        : defaultChildType.createAndFill();

                if (!defaultChildNode) {
                    return false;
                }

                const $childPos = state.doc.resolve(detailsContent.pos + 1);
                const lastChildIndex = childCount - 1;
                const lastChildNode = detailsContent.node.child(lastChildIndex);
                const lastChildPos = $childPos.posAtIndex(lastChildIndex, detailsContent.depth);
                const lastChildNodeIsEmpty = lastChildNode.eq(defaultChildNode);

                if (!lastChildNodeIsEmpty) {
                    return false;
                }

                const above = $from.node(-3);
                if (!above) {
                    return false;
                }

                const after = $from.indexAfter(-3);
                const type = defaultBlockAt(above.contentMatchAt(after));
                if (!type || !above.canReplaceWith(after, after, type)) {
                    return false;
                }

                const node = type.createAndFill();

                if (!node) {
                    return false;
                }

                const { tr } = state;
                const pos = $from.after(-2);
                tr.replaceWith(pos, pos, node);
                const $pos = tr.doc.resolve(pos);
                const newSelection = Selection.near($pos, 1);
                tr.setSelection(newSelection);
                const deleteFrom = lastChildPos;
                const deleteTo = lastChildPos + lastChildNode.nodeSize;
                tr.delete(deleteFrom, deleteTo);
                tr.scrollIntoView();
                view.dispatch(tr);
                return true;
            },
        };
    },
});
