<script setup>
    import { createMessage } from '@formkit/core';
    import axios from 'axios';
    import { inject, nextTick, onBeforeUnmount, onMounted, ref } from 'vue';
    import { passwordStorageKey } from '../passwordStorage.js';

    const props = defineProps({
        context: Object,
    });

    const typedValue = ref('');
    const storedSecretId = ref(props.context.node.value ?? null);
    const isEditing = ref(storedSecretId.value === null);
    const isDirty = ref(false);
    const isStoring = ref(false);
    const storagePromise = ref(null);
    const passwordStorage = inject(passwordStorageKey, null);
    let unregister = null;

    onMounted(() => {
        unregister = passwordStorage?.register(props.context.id, {
            flush: storeSecret,
            needsFlush: () => storagePromise.value !== null || isDirty.value,
        });
    });

    onBeforeUnmount(() => unregister?.());

    function editSecret() {
        typedValue.value = '';
        isDirty.value = false;
        isEditing.value = true;
        nextTick(() => document.getElementById(props.context.id)?.focus());
    }

    function removeSecret() {
        typedValue.value = '';
        isDirty.value = true;
        isEditing.value = true;

        return storeSecret(true);
    }

    function storeSecret(shouldRemove = false) {
        props.context.node.store.remove('secret-storage-failed');

        if (storagePromise.value) {
            return storagePromise.value;
        }

        if (!isDirty.value) {
            isEditing.value = storedSecretId.value === null;

            return Promise.resolve(true);
        }

        if (!shouldRemove && typedValue.value === '') {
            isDirty.value = false;
            isEditing.value = storedSecretId.value === null;

            return Promise.resolve(true);
        }

        isStoring.value = true;
        props.context.node.store.set(
            createMessage({
                blocking: true,
                key: 'storing-secret',
                value: 'Saving password securely.',
                visible: false,
            }),
        );

        storagePromise.value = (async () => {
            try {
                const { data } = await axios.post(props.context.storeUrl, {
                    value: shouldRemove ? null : typedValue.value,
                    previous_secret_id: storedSecretId.value,
                });

                typedValue.value = '';
                storedSecretId.value = data.id;
                isEditing.value = data.id === null;
                isDirty.value = false;
                await props.context.node.input(data.id);

                return true;
            } catch {
                props.context.node.store.set(
                    createMessage({
                        blocking: true,
                        key: 'secret-storage-failed',
                        value: 'The password could not be saved. Please try again.',
                    }),
                );

                return false;
            } finally {
                isStoring.value = false;
                storagePromise.value = null;
                props.context.node.store.remove('storing-secret');
            }
        })();

        return storagePromise.value;
    }
</script>

<template>
    <div v-if="storedSecretId !== null && !isEditing" class="flex w-full items-center gap-3 px-3 py-2">
        <span class="text-gray-500">••••••••</span>
        <template v-if="!context.disabled">
            <button type="button" class="text-sm font-medium text-brand-600" @click="editSecret">Edit</button>
            <button type="button" class="text-sm font-medium text-red-600" @click="removeSecret">Remove</button>
        </template>
    </div>

    <input
        v-else
        v-model="typedValue"
        type="password"
        autocomplete="new-password"
        :id="context.id"
        :name="context.node.name"
        :disabled="isStoring || context.disabled"
        :class="context.classes.input"
        @input="isDirty = true"
        @blur="storeSecret()"
    />
</template>
