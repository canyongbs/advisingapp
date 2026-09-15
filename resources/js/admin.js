/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/
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

// Filament's rich editor renders a custom block's header label as a plain
// text node (see `fi-fo-rich-editor-custom-block-heading` in
// `filament/forms`), so it cannot display the "Mapped"/"Unmapped" and
// "Required"/"Optional" badges shown in the forms field builder. This
// progressively enhances that header after Filament renders it, without
// overriding any of Filament's own rich editor JavaScript.
const richEditorCustomBlockBadgeClasses = {
    neutral: 'bg-gray-200 text-black dark:bg-white/10 dark:text-white',
    subtle: 'border border-gray-300 bg-white text-gray-600 dark:border-white/20 dark:bg-transparent dark:text-gray-400',
};

function createRichEditorCustomBlockBadge(label, variant) {
    const badge = document.createElement('span');

    badge.className = `inline-flex items-center whitespace-nowrap rounded-full px-2 py-0.5 text-xs font-medium ${richEditorCustomBlockBadgeClasses[variant]}`;
    badge.textContent = label;

    return badge;
}

function decorateRichEditorCustomBlock(block) {
    // Only decorate blocks inside a rich editor that explicitly opts in via
    // `data-mapped-block-types` (set from FormFieldBlockRegistry::getMappedBlockTypes()).
    // Editors for unrelated custom blocks (e.g. the survey builder) don't set this
    // attribute, so they are left alone instead of being mislabelled as "Unmapped".
    const fieldBuilder = block.closest('[data-mapped-block-types]');

    if (!fieldBuilder) {
        return;
    }

    const header = block.querySelector(':scope > .fi-fo-rich-editor-custom-block-header');
    const heading = header?.querySelector(':scope > .fi-fo-rich-editor-custom-block-heading');

    if (!header || !heading || heading.querySelector(':scope > .fi-fo-rich-editor-custom-block-badges')) {
        return;
    }

    let config = {};

    try {
        config = JSON.parse(block.getAttribute('data-config') ?? '{}') ?? {};
    } catch {
        return;
    }

    const isMapped = fieldBuilder
        .getAttribute('data-mapped-block-types')
        .split(',')
        .includes(block.getAttribute('data-id'));

    const badges = document.createElement('span');
    badges.className = 'fi-fo-rich-editor-custom-block-badges inline-flex items-center';

    badges.appendChild(createRichEditorCustomBlockBadge(isMapped ? 'Mapped' : 'Unmapped', 'neutral'));

    const requiredFlags = Object.entries(config)
        .filter(([key, value]) => /required$/i.test(key) && typeof value === 'boolean')
        .map(([, value]) => value);

    if (requiredFlags.length > 0) {
        const isRequired = requiredFlags.some(Boolean);

        badges.appendChild(createRichEditorCustomBlockBadge(isRequired ? 'Required' : 'Optional', 'subtle'));
    }

    // Appended inside the heading, rather than as its sibling, so the badges sit
    // directly beside the label text instead of being pushed to the far right by
    // the heading's `flex: 1` in Filament's rich editor header layout.
    heading.appendChild(badges);
}

function observeRichEditorCustomBlocks() {
    document.querySelectorAll('div[data-type="customBlock"]').forEach(decorateRichEditorCustomBlock);

    new MutationObserver((mutations) => {
        for (const mutation of mutations) {
            for (const node of mutation.addedNodes) {
                if (!(node instanceof HTMLElement)) {
                    continue;
                }

                if (node.matches('div[data-type="customBlock"]')) {
                    decorateRichEditorCustomBlock(node);
                }

                node.querySelectorAll?.('div[data-type="customBlock"]').forEach(decorateRichEditorCustomBlock);
            }
        }
    }).observe(document.body, {
        childList: true,
        subtree: true,
    });
}

document.addEventListener('DOMContentLoaded', observeRichEditorCustomBlocks);
