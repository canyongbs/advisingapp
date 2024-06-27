<x-filament-tiptap-editor::button
    style="display: none;"
    x-show="tools.includes('color') && editor().isActive('textStyle', updatedAt)"
    action="editor().chain().focus().unsetColor().run()"
    label="{{ trans('filament-tiptap-editor::editor.remove_color') }}"
    icon="remove-color"
/>
