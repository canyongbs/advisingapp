import {
    Extension
} from "@tiptap/core";
import {
    Plugin,
    PluginKey
} from "@tiptap/pm/state";

const allowedMimeTypes = ['image/png', 'image/jpeg', 'image/gif', 'image/webp'];

const LocalFilesPlugin = ({
           key,
           editor,
       }) => new Plugin({
    key: key || new PluginKey("localFiles"),
    props: {
        handleDrop(editorView, event) {
            if (! event.dataTransfer?.files.length) {
                return false
            }

            const position = editorView.posAtCoords({
                left: event.clientX,
                top: event.clientY
            });

            let files = Array.from(event.dataTransfer.files);

            files = files.filter((file) => allowedMimeTypes.includes(file.type));

            if (! files.length) {
                return false;
            }

            event.preventDefault();
            event.stopPropagation();

            files.forEach((file) => {
                const fileReader = new FileReader()

                fileReader.readAsDataURL(file)
                fileReader.onload = () => {
                    editor.chain().insertContentAt(position?.pos ?? 0, {
                        type: 'image',
                        attrs: {
                            src: fileReader.result,
                        },
                    }).focus().run()
                }
            })

            return true;
        },
        handlePaste(editorView, event) {
            if (! event.clipboardData?.files.length) {
                return false
            }

            let files = Array.from(event.clipboardData.files);

            const htmlClipboardData = event.clipboardData.getData("text/html");

            files = files.filter((file) => allowedMimeTypes.includes(file.type));

            if (! files.length) {
                return false;
            }

            event.preventDefault();
            event.stopPropagation();

            files.forEach((file) => {
                if (htmlClipboardData) {
                    // if there is htmlContent, stop manual insertion & let other extensions handle insertion via inputRule
                    // you could extract the pasted file from this url string and upload it to a server for example
                    console.log(htmlClipboardData) // eslint-disable-line no-console

                    return false
                }

                const fileReader = new FileReader()

                fileReader.readAsDataURL(file)
                fileReader.onload = () => {
                    editor.chain().insertContentAt(editor.state.selection.anchor, {
                        type: 'image',
                        attrs: {
                            src: fileReader.result,
                        },
                    }).focus().run()
                }
            })

            return ! htmlClipboardData.length;
        }
    }
})

export const LocalFilesExtension = Extension.create({
    name: "localFiles",
    addProseMirrorPlugins() {
        return [LocalFilesPlugin({
            key: new PluginKey(this.name),
            editor: this.editor,
        })]
    }
})
