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
import { Editor, posToDOMRect } from '@tiptap/core';
import { Plugin, PluginKey } from '@tiptap/pm/state';
import tippy from 'tippy.js';

export class FloatingMenuView {
    shouldShow = ({ view, state }) => {
        const { selection } = state;
        const { $anchor, empty } = selection;
        const isRootDepth = $anchor.depth === 1;
        const isEmptyTextBlock =
            $anchor.parent.isTextblock && !$anchor.parent.type.spec.code && !$anchor.parent.textContent;

        return !(!view.hasFocus() || !empty || !isRootDepth || !isEmptyTextBlock || !this.editor.isEditable);
    };

    constructor({ editor, element, view, tippyOptions = {}, shouldShow }) {
        this.editor = editor;
        this.element = element;
        this.view = view;

        if (shouldShow) {
            this.shouldShow = shouldShow;
        }

        this.element.addEventListener('mousedown', this.mousedownHandler, { capture: true });
        this.editor.on('focus', this.focusHandler);
        this.editor.on('blur', this.blurHandler);
        this.tippyOptions = tippyOptions;
        // Detaches menu content from its current parent
        // this.element.remove()
        this.element.style.visibility = 'hidden';
        this.element.style.position = 'absolute';
    }

    mousedownHandler = () => {
        this.preventHide = true;
    };

    focusHandler = () => {
        // we use `setTimeout` to make sure `selection` is already updated
        setTimeout(() => this.update(this.editor.view));
    };

    blurHandler = ({ event }) => {
        if (this.preventHide) {
            this.preventHide = false;

            return;
        }

        if (event?.relatedTarget && this.element.parentNode?.contains(event.relatedTarget)) {
            return;
        }

        this.hide();
    };

    tippyBlurHandler = (event) => {
        this.blurHandler({ event });
    };

    createTooltip() {
        const { element: editorElement } = this.editor.options;
        const editorIsAttached = !!editorElement.parentElement;

        if (this.tippy || !editorIsAttached) {
            return;
        }

        this.tippy = tippy(editorElement, {
            duration: 0,
            getReferenceClientRect: null,
            content: this.element,
            interactive: true,
            trigger: 'manual',
            placement: 'right',
            hideOnClick: 'toggle',
            ...this.tippyOptions,
        });

        // maybe we have to hide tippy on its own blur event as well
        if (this.tippy.popper.firstChild) {
            this.tippy.popper.firstChild.addEventListener('blur', this.tippyBlurHandler);
        }
    }

    update(view, oldState) {
        const { state } = view;
        const { doc, selection } = state;
        const { from, to } = selection;
        const isSame = oldState && oldState.doc.eq(doc) && oldState.selection.eq(selection);

        if (isSame) {
            return;
        }

        this.createTooltip();

        const shouldShow = this.shouldShow?.({
            editor: this.editor,
            view,
            state,
            oldState,
        });

        if (!shouldShow) {
            this.hide();

            return;
        }

        this.tippy?.setProps({
            getReferenceClientRect: this.tippyOptions?.getReferenceClientRect || (() => posToDOMRect(view, from, to)),
        });

        this.show();
    }

    show() {
        this.element.style.position = 'relative';
        this.element.style.visibility = 'visible';
        this.tippy?.show();
    }

    hide() {
        this.tippy?.hide();
    }

    destroy() {
        if (this.tippy?.popper.firstChild) {
            this.tippy.popper.firstChild.removeEventListener('blur', this.tippyBlurHandler);
        }
        this.tippy?.destroy();
        this.element.removeEventListener('mousedown', this.mousedownHandler, { capture: true });
        this.editor.off('focus', this.focusHandler);
        this.editor.off('blur', this.blurHandler);
    }
}

export const FloatingMenuPlugin = (options) => {
    return new Plugin({
        key: typeof options.pluginKey === 'string' ? new PluginKey(options.pluginKey) : options.pluginKey,
        view: (view) => new FloatingMenuView({ view, ...options }),
    });
};
