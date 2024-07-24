{{--
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
--}}
<x-filament-tiptap-editor::dropdown-button
    label="{{ trans('filament-tiptap-editor::editor.color.label') }}"
    active="color"
    icon="color"
    :list="false"
>
    <div
        class="relative flex-1 p-1"
        x-data="{
            state: editor().getAttributes('textStyle').color || '#000000',
        
            init: function() {
                if (!(this.state === null || this.state === '')) {
                    this.setState(this.state)
                }
            },
        
            setState: function(value) {
                this.state = value
            }
        }"
        x-on:keydown.esc="isOpen() && $event.stopPropagation()"
    >
        <tiptap-hex-color-picker
            x-bind:color="state"
            x-on:color-changed="setState($event.detail.value)"
        ></tiptap-hex-color-picker>

        <div class="mt-2 flex w-full gap-2">
            <x-filament::button
                class="flex-1"
                x-on:click="editor().chain().focus().setColor(state).run(); $dispatch('close-panel')"
                size="sm"
            >
                {{ trans('filament-tiptap-editor::editor.color.choose') }}
            </x-filament::button>

            <x-filament::button
                class="flex-1"
                x-on:click="editor().chain().focus().unsetColor().run(); $dispatch('close-panel')"
                size="sm"
                color="danger"
            >
                {{ trans('filament-tiptap-editor::editor.color.remove') }}
            </x-filament::button>
        </div>
    </div>
</x-filament-tiptap-editor::dropdown-button>
