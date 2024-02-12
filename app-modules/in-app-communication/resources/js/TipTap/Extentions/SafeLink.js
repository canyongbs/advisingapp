import Link from '@tiptap/extension-link';
import { mergeAttributes } from '@tiptap/core';

export const SafeLink = Link.extend({
    renderHTML({ HTMLAttributes }) {
        // This is directly pulled from the Link extension - leave as is.
        // eslint-disable-next-line no-script-url
        if (HTMLAttributes.href?.startsWith('javascript:')) {
            return [
                'button',
                mergeAttributes(this.options.HTMLAttributes, { ...HTMLAttributes, 'data-safe-link': 'true', href: '' }),
                0,
            ];
        }

        return [
            'button',
            mergeAttributes(this.options.HTMLAttributes, { ...HTMLAttributes, 'data-safe-link': 'true' }),
            0,
        ];
    },
});
