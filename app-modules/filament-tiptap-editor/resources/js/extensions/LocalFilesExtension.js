import { Extension } from '@tiptap/core';
import { Plugin, PluginKey } from '@tiptap/pm/state';

const allowedMimeTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];

const dispatchFormEvent = (editorView, name, detail = {}) => {
    editorView.dom.closest('form')?.dispatchEvent(
        new CustomEvent(name, {
            composed: true,
            cancelable: true,
            detail,
        }),
    )
};

const LocalFilesPlugin = ({ key, editor, getFileAttachmentUrl, statePath, upload, uploadingMessage }) =>
    new Plugin({
        key: key || new PluginKey('localFiles'),
        props: {
            handleDrop(editorView, event) {
                if (!event.dataTransfer?.files.length) {
                    return false;
                }

                const files = Array.from(event.dataTransfer.files).filter((file) =>
                    allowedMimeTypes.includes(file.type),
                );

                if (!files.length) {
                    return false;
                }

                dispatchFormEvent(editorView, 'form-processing-started', {
                    message: uploadingMessage
                });

                event.preventDefault();
                event.stopPropagation();

                const position = editorView.posAtCoords({
                    left: event.clientX,
                    top: event.clientY,
                });

                files.forEach((file, fileIndex) => {
                    editor.setEditable(false);

                    const fileReader = new FileReader();

                    fileReader.readAsDataURL(file);
                    fileReader.onload = () => {
                        editor
                            .chain()
                            .insertContentAt(position?.pos ?? 0, {
                                type: 'image',
                                attrs: {
                                    class: 'filament-tiptap-loading-image',
                                    src: fileReader.result,
                                },
                            })
                            .run();
                    };

                    let fileKey = (
                        [1e7] +
                        -1e3 +
                        -4e3 +
                        -8e3 +
                        -1e11
                    ).replace(/[018]/g, (c) =>
                        (
                            c ^
                            (crypto.getRandomValues(new Uint8Array(1))[0] &
                                (15 >> (c / 4)))
                        ).toString(16),
                    )

                    upload(`componentFileAttachments.${statePath}.${fileKey}`, file, () => {
                        getFileAttachmentUrl(fileKey).then((url) => {
                            if (! url) {
                                return;
                            }

                            editor
                                .chain()
                                .insertContentAt({ from: position?.pos ?? 0, to: (position?.pos ?? 0) + 1 }, {
                                    type: 'image',
                                    attrs: {
                                        id: fileKey,
                                        src: url,
                                    },
                                })
                                .run();

                            editor.setEditable(true);

                            if (fileIndex === (files.length - 1)) {
                                dispatchFormEvent(editorView, 'form-processing-finished');
                            }
                        })
                    })
                });

                return true;
            },
            handlePaste(editorView, event) {
                if (!event.clipboardData?.files.length) {
                    return false;
                }

                const files = Array.from(event.clipboardData.files).filter((file) =>
                    allowedMimeTypes.includes(file.type),
                );

                if (!files.length) {
                    return false;
                }

                event.preventDefault();
                event.stopPropagation();

                dispatchFormEvent(editorView, 'form-processing-started', {
                    message: uploadingMessage
                });

                files.forEach((file, fileIndex) => {
                    editor.setEditable(false);

                    const fileReader = new FileReader();

                    fileReader.readAsDataURL(file);
                    fileReader.onload = () => {
                        editor
                            .chain()
                            .insertContentAt(editor.state.selection.anchor, {
                                type: 'image',
                                attrs: {
                                    class: 'filament-tiptap-loading-image',
                                    src: fileReader.result,
                                },
                            })
                            .run();
                    };

                    let fileKey = (
                        [1e7] +
                        -1e3 +
                        -4e3 +
                        -8e3 +
                        -1e11
                    ).replace(/[018]/g, (c) =>
                        (
                            c ^
                            (crypto.getRandomValues(new Uint8Array(1))[0] &
                                (15 >> (c / 4)))
                        ).toString(16),
                    )

                    upload(`componentFileAttachments.${statePath}.${fileKey}`, file, () => {
                        getFileAttachmentUrl(fileKey).then((url) => {
                            if (! url) {
                                return;
                            }

                            editor
                                .chain()
                                .insertContentAt({ from: editor.state.selection.anchor - 1, to: editor.state.selection.anchor }, {
                                    type: 'image',
                                    attrs: {
                                        id: fileKey,
                                        src: url,
                                    },
                                })
                                .run();

                            editor.setEditable(true);

                            if (fileIndex === (files.length - 1)) {
                                dispatchFormEvent(editorView, 'form-processing-finished');
                            }
                        })
                    })
                });

                return true;
            },
        },
    });

export const LocalFilesExtension = Extension.create({
    name: 'localFiles',

    addOptions() {
        return {
            getFileAttachmentUrl: null,
            statePath: null,
            upload: null,
            uploadingMessage: null,
        };
    },

    addProseMirrorPlugins() {
        return [
            LocalFilesPlugin({
                key: new PluginKey(this.name),
                editor: this.editor,
                getFileAttachmentUrl: this.options.getFileAttachmentUrl,
                statePath: this.options.statePath,
                upload: this.options.upload,
                uploadingMessage: this.options.uploadingMessage,
            }),
        ];
    },
});
