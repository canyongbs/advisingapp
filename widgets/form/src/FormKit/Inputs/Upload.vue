<!--
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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
-->
<script setup>
import { createMessage } from '@formkit/core';
import axios from 'axios';
import { computed, nextTick, ref } from 'vue';
import { consumer } from '../../../../../portals/resource-hub/src/Services/Consumer.js';

import vueFilePond from 'vue-filepond';

import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type';

import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css';
import 'filepond/dist/filepond.min.css';

const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginFileValidateSize);

const props = defineProps({
    context: Object,
});
console.log(props.context,'context');

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
            // const data = await get(props.context.uploadUrl, {
            //     params: { filename: file.name }
            // })
            const data = await axios
                .get(props.context.uploadUrl, {
                    params: { filename: file.name },
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
                            console.log('Error sending photo:', err);
                            return null;
                        });
                })
                .catch((err) => {
                    console.log('Error fetching upload URL:', err);
                    return null;
                })
                .finally(() => {
                    props.context.node.store.remove(`uploading.${index}`);
                });

            if (!data || !data.path) {
                error('Invalid upload response');
                return;
            }

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

    const isDuplicate = uploadedFiles.value.some((existingFile) => existingFile.originalFileName === file.file.name);

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
