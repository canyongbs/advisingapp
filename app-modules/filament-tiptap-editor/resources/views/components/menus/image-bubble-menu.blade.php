@props([
    'statePath' => null,
    'tools' => [],
])

@if (in_array('media', $tools))
    <div
        class="flex items-center gap-1"
        style="display: none;"
        x-show="editor().isActive('image', updatedAt)"
    >
        <x-filament-tiptap-editor::tools.edit-media
            :state-path="$statePath"
            icon="edit"
            :active="false"
        />
    </div>
@endif
