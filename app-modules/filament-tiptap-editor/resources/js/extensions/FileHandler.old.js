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
import { Extension as e } from '@tiptap/core';
import { Plugin as t, PluginKey as o } from '@tiptap/pm/state';
const r = ({ key: e, editor: r, onPaste: i, onDrop: a, allowedMimeTypes: n }) =>
        new t({
            key: e || new o('fileHandler'),
            props: {
                handleDrop(e, t) {
                    var o;
                    if (!a) return !1;
                    if (!(null === (o = t.dataTransfer) || void 0 === o ? void 0 : o.files.length)) return !1;
                    const i = e.posAtCoords({
                        left: t.clientX,
                        top: t.clientY,
                    });
                    let l = Array.from(t.dataTransfer.files);
                    return (
                        n && (l = l.filter((e) => n.includes(e.type))),
                        0 !== l.length &&
                            (t.preventDefault(), t.stopPropagation(), a(r, l, (null == i ? void 0 : i.pos) || 0), !0)
                    );
                },
                handlePaste(e, t) {
                    var o;
                    if (!i) return !1;
                    if (!(null === (o = t.clipboardData) || void 0 === o ? void 0 : o.files.length)) return !1;
                    let a = Array.from(t.clipboardData.files);
                    const l = t.clipboardData.getData('text/html');
                    return (
                        n && (a = a.filter((e) => n.includes(e.type))),
                        0 !== a.length && (t.preventDefault(), t.stopPropagation(), i(r, a, l), !(l.length > 0))
                    );
                },
            },
        }),
    i = e.create({
        name: 'fileHandler',
        addOptions: () => ({
            onPaste: void 0,
            onDrop: void 0,
            allowedMimeTypes: void 0,
        }),
        addProseMirrorPlugins() {
            return [
                r({
                    key: new o(this.name),
                    editor: this.editor,
                    allowedMimeTypes: this.options.allowedMimeTypes,
                    onDrop: this.options.onDrop,
                    onPaste: this.options.onPaste,
                }),
            ];
        },
    });
export { r as FileHandlePlugin, i as FileHandler, i as default };
