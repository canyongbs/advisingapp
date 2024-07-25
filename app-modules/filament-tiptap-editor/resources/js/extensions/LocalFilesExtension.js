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
    );
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
                    message: uploadingMessage,
                });

                event.preventDefault();
                event.stopPropagation();

                const position = editorView.posAtCoords({
                    left: event.clientX,
                    top: event.clientY,
                });

                files.forEach((file, fileIndex) => {
                    editor.setEditable(false);
                    editorView.dom.dispatchEvent(new CustomEvent('tiptap-uploading-file', { bubbles: true, detail: { statePath } }));

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

                    let fileKey = ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) =>
                        (c ^ (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))).toString(16),
                    );

                    upload(`componentFileAttachments.${statePath}.${fileKey}`, file, () => {
                        getFileAttachmentUrl(fileKey).then((url) => {
                            if (!url) {
                                return;
                            }

                            editor
                                .chain()
                                .setNodeSelection(position?.pos ?? 0)
                                .deleteSelection()
                                .insertContentAt(
                                    position?.pos ?? 0,
                                    {
                                        type: 'image',
                                        attrs: {
                                            id: fileKey,
                                            src: url,
                                        },
                                    },
                                )
                                .run();

                            editor.setEditable(true);
                            editorView.dom.dispatchEvent(new CustomEvent('tiptap-uploaded-file', { bubbles: true, detail: { statePath } }));

                            if (fileIndex === files.length - 1) {
                                dispatchFormEvent(editorView, 'form-processing-finished');
                            }
                        });
                    });
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
                    message: uploadingMessage,
                });

                files.forEach((file, fileIndex) => {
                    editor.setEditable(false);
                    editorView.dom.dispatchEvent(new CustomEvent('tiptap-uploading-file', { bubbles: true, detail: { statePath } }));

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

                    let fileKey = ([1e7] + -1e3 + -4e3 + -8e3 + -1e11).replace(/[018]/g, (c) =>
                        (c ^ (crypto.getRandomValues(new Uint8Array(1))[0] & (15 >> (c / 4)))).toString(16),
                    );

                    upload(`componentFileAttachments.${statePath}.${fileKey}`, file, () => {
                        getFileAttachmentUrl(fileKey).then((url) => {
                            if (!url) {
                                return;
                            }

                            editor
                                .chain()
                                .setNodeSelection(editor.state.selection.anchor)
                                .deleteSelection()
                                .insertContentAt(
                                    editor.state.selection.anchor,
                                    {
                                        type: 'image',
                                        attrs: {
                                            id: fileKey,
                                            src: url,
                                        },
                                    },
                                )
                                .run();

                            editor.setEditable(true);
                            editorView.dom.dispatchEvent(new CustomEvent('tiptap-uploaded-file', { bubbles: true, detail: { statePath } }));

                            if (fileIndex === files.length - 1) {
                                dispatchFormEvent(editorView, 'form-processing-finished');
                            }
                        });
                    });
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
