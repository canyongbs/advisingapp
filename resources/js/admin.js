// https://github.com/livewire/livewire/discussions/5923#discussioncomment-9202549

const original = window.history.replaceState;
let timer = Date.now();

let timeout = null;
let lastArgs = null;

window.history.replaceState = function (...args) {
    const time = Date.now();

    if (time - timer < 300) {
        lastArgs = args;

        if (timeout) {
            clearTimeout(timeout);
        }

        timeout = setTimeout(() => {
            original.apply(this, lastArgs);

            timeout = null;
            lastArgs = null;
        }, 100);

        return;
    }

    timer = time;

    original.apply(this, args);
};
