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
