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
