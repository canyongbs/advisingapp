<script setup>
    import { createMessage } from '@formkit/core';
    import axios from 'axios';
    import { computed, nextTick, ref } from 'vue';
    import { consumer } from '../../../../../portals/knowledge-management/src/Services/Consumer.js';

    import vueFilePond from 'vue-filepond';

    import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
    import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

    import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
    import 'filepond/dist/filepond.min.css';

    const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginFileValidateSize);

    const props = defineProps({
        context: Object,
    });

    const field = ref(null);
    const uploadedFiles = ref([]);
    const fileIndexCounter = ref(0);

    const serverOptions = computed(() => ({
        process: async (fieldName, file, metadata, load, error, progress, abort) => {
            const fileIndex = uploadedFiles.value.findIndex((f) => f.originalFileName === file.name);
            if (fileIndex !== -1) {
                props.context.node.store.set(
                    createMessage({
                        blocking: true,
                        key: `uploaded.${fileIndex}`,
                        value: `File already exists with name: ${file.name}.`,
                    }),
                );
                load();
                return;
            }
            const index = fileIndexCounter.value++;
            const { get } = consumer();
            try {
                const data = await get(props.context.uploadUrl, {
                    filename: file.name,
                })
                    .then(async (response) => {
                        const { url, path } = response.data;

                        return axios
                            .put(url, file, {
                                headers: {
                                    'Content-Type': file.type,
                                },
                            })
                            .then(() => {
                                return {
                                    originalFileName: file.name,
                                    path: path,
                                };
                            })
                            .catch(() => {
                                return null;
                            });
                    })
                    .catch(() => {
                        return null;
                    })
                    .finally(() => {
                        props.context.node.store.remove(`uploading.${index}`);
                    });

                const { path } = data;

                uploadedFiles.value.push({
                    originalFileName: file.name,
                    path: path,
                });
                Promise.all(uploadedFiles.value).then((files) => {
                    props.context.node.input(files);
                });
                load(path);
            } catch (err) {
                console.error('Upload error:', err);
                error('Upload failed');
            }

            return {
                abort: () => {
                    console.log('Upload aborted');
                    abort();
                },
            };
        },
        revert: async (uniqueFileId, load) => {
            try {
                const fileIndex = uploadedFiles.value.findIndex((f) => f.path === uniqueFileId);
                if (fileIndex !== -1) {
                    uploadedFiles.value.splice(fileIndex, 1);
                } else {
                    console.warn('File not found in uploadedFiles array:', uniqueFileId);
                }

                load();
                Promise.all(uploadedFiles.value).then((files) => {
                    props.context.node.input(files);
                });
            } catch (err) {
                console.error('Failed to remove file:', err);
            }
        },
    }));

    const handleFileAdd = (error, file) => {
        if (error) {
            console.error('Error adding file:', error);
            return;
        }

        const isDuplicate = uploadedFiles.value.some(
            (existingFile) => existingFile.originalFileName === file.file.name,
        );

        if (isDuplicate) {
            props.context.node.store.set(
                createMessage({
                    blocking: true,
                    key: `Already exists.${file.file.name}`,
                    value: `The file "${file.file.name}" has already been uploaded.`,
                }),
            );
            nextTick(() => {
                const pond = field.value;
                pond.removeFile(file.id);
            });
            return;
        }
    };
</script>

<template>
    <file-pond
        ref="field"
        label-idle="Drop files here or <span class='filepond--label-action'>Browse</span>"
        :allow-multiple="context.multiple"
        :accepted-file-types="context.accept.join(', ')"
        :maxFiles="context.limit"
        :maxFileSize="context.size + 'MB'"
        :files="uploadedFiles"
        :server="serverOptions"
        @addfile="handleFileAdd"
        :credits="false"
    />
</template>

<style scoped></style>
