import { submitForm } from '@formkit/core';

export const passwordStorageKey = Symbol('passwordStorage');

export function createPasswordStorageCoordinator() {
    const fields = new Map();

    function register(id, field) {
        fields.set(id, field);

        return () => fields.delete(id);
    }

    function startFlush() {
        const dirtyFields = [...fields.values()].filter((field) => field.needsFlush());

        if (dirtyFields.length === 0) {
            return null;
        }

        return Promise.all(dirtyFields.map((field) => field.flush())).then((results) => results.every(Boolean));
    }

    async function flushAll() {
        return (await startFlush()) ?? true;
    }

    function prepareSubmit(event, node) {
        const flush = startFlush();

        if (!flush) {
            return;
        }

        flush.then(async (successful) => {
            if (!successful) {
                return;
            }

            await node.settled;
            submitForm(node.props.id);
        });
    }

    return { flushAll, prepareSubmit, register };
}
