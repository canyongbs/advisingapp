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
@props([
    'statePath' => null,
    'tools' => [],
])

@if (in_array('table', $tools))
    <div
        class="flex items-center gap-1"
        style="display: none;"
        x-show="editor().isActive('table', updatedAt)"
    >
        <x-filament-tiptap-editor::button
            action="editor().chain().focus().addColumnBefore().run()"
            icon="table-add-column-before"
            label="{{ trans('filament-tiptap-editor::editor.table.add_column_before') }}"
        />

        <x-filament-tiptap-editor::button
            action="editor().chain().focus().addColumnAfter().run()"
            icon="table-add-column-after"
            label="{{ trans('filament-tiptap-editor::editor.table.add_column_after') }}"
        />

        <x-filament-tiptap-editor::button
            action="editor().chain().focus().deleteColumn().run()"
            icon="table-delete-column"
            label="{{ trans('filament-tiptap-editor::editor.table.delete_column') }}"
        />

        <x-filament-tiptap-editor::button
            action="editor().chain().focus().addRowBefore().run()"
            icon="table-add-row-before"
            label="{{ trans('filament-tiptap-editor::editor.table.add_row_before') }}"
        />

        <x-filament-tiptap-editor::button
            action="editor().chain().focus().addRowAfter().run()"
            icon="table-add-row-after"
            label="{{ trans('filament-tiptap-editor::editor.table.add_row_after') }}"
        />

        <x-filament-tiptap-editor::button
            action="editor().chain().focus().deleteRow().run()"
            icon="table-delete-row"
            label="{{ trans('filament-tiptap-editor::editor.table.delete_row') }}"
        />

        <x-filament-tiptap-editor::button
            action="editor().chain().focus().mergeCells().run()"
            icon="table-merge-cells"
            label="{{ trans('filament-tiptap-editor::editor.table.merge_cells') }}"
        />

        <x-filament-tiptap-editor::button
            action="editor().chain().focus().splitCell().run()"
            icon="table-split-cells"
            label="{{ trans('filament-tiptap-editor::editor.table.split_cell') }}"
        />

        <x-filament-tiptap-editor::button
            action="editor().chain().focus().deleteTable().run()"
            icon="table-delete"
            label="{{ trans('filament-tiptap-editor::editor.table.delete_table') }}"
        />
    </div>
@endif
