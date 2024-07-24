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
    'editor' => null,
])

@php
    $layouts = $editor->getGridLayouts();
@endphp

<x-filament-tiptap-editor::dropdown-button
    label="{{ trans('filament-tiptap-editor::editor.grid.label') }}"
    active="grid"
    icon="grid"
>
    @if (in_array('two-columns', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item action="editor().chain().focus().insertGrid({ cols: 2 }).run()">
            {{ trans('filament-tiptap-editor::editor.grid.two_columns') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('three-columns', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item action="editor().chain().focus().insertGrid({ cols: 3 }).run()">
            {{ trans('filament-tiptap-editor::editor.grid.three_columns') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('four-columns', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item action="editor().chain().focus().insertGrid({ cols: 4 }).run()">
            {{ trans('filament-tiptap-editor::editor.grid.four_columns') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('five-columns', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item action="editor().chain().focus().insertGrid({ cols: 5 }).run()">
            {{ trans('filament-tiptap-editor::editor.grid.five_columns') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('fixed-two-columns', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item
            action="editor().chain().focus().insertGrid({ cols: 2, type: 'fixed' }).run()"
        >
            {{ trans('filament-tiptap-editor::editor.grid.fixed_two_columns') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('fixed-three-columns', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item
            action="editor().chain().focus().insertGrid({ cols: 3, type: 'fixed' }).run()"
        >
            {{ trans('filament-tiptap-editor::editor.grid.fixed_three_columns') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('fixed-four-columns', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item
            action="editor().chain().focus().insertGrid({ cols: 4, type: 'fixed' }).run()"
        >
            {{ trans('filament-tiptap-editor::editor.grid.fixed_four_columns') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('fixed-five-columns', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item
            action="editor().chain().focus().insertGrid({ cols: 5, type: 'fixed' }).run()"
        >
            {{ trans('filament-tiptap-editor::editor.grid.fixed_five_columns') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('asymmetric-left-thirds', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item
            action="editor().chain().focus().insertGrid({ cols: 2, type: 'asymetric-left-thirds' }).run()"
        >
            {{ trans('filament-tiptap-editor::editor.grid.asymmetric_left_thirds') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('asymmetric-right-thirds', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item
            action="editor().chain().focus().insertGrid({ cols: 2, type: 'asymetric-right-thirds' }).run()"
        >
            {{ trans('filament-tiptap-editor::editor.grid.asymmetric_right_thirds') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('asymmetric-left-fourths', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item
            action="editor().chain().focus().insertGrid({ cols: 2, type: 'asymetric-left-fourths' }).run()"
        >
            {{ trans('filament-tiptap-editor::editor.grid.asymmetric_left_fourths') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif

    @if (in_array('asymmetric-right-fourths', $layouts))
        <x-filament-tiptap-editor::dropdown-button-item
            action="editor().chain().focus().insertGrid({ cols: 2, type: 'asymetric-right-fourths' }).run()"
        >
            {{ trans('filament-tiptap-editor::editor.grid.asymmetric_right_fourths') }}
        </x-filament-tiptap-editor::dropdown-button-item>
    @endif
</x-filament-tiptap-editor::dropdown-button>
