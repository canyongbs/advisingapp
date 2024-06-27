@props([
    'statePath' => null,
    'tools' => [],
])

@if (in_array('link', $tools))
    <div
        class="flex items-center gap-1"
        style="display: none;"
        x-show="editor().isActive('link', updatedAt)"
    >
        <span
            class="max-w-xs overflow-hidden truncate whitespace-nowrap"
            x-text="editor().getAttributes('link', updatedAt).href"
        ></span>
        <x-filament-tiptap-editor::tools.link
            :state-path="$statePath"
            icon="edit"
            :active="false"
            label="{{ trans('filament-tiptap-editor::editor.link.edit') }}"
        />
        <x-filament-tiptap-editor::tools.remove-link />
    </div>
@endif
