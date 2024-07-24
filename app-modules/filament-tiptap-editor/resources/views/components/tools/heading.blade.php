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
    label="{{ trans('filament-tiptap-editor::editor.heading.label') }}"
    active="heading"
    icon="heading"
    indicator="editor().isActive('heading', updatedAt) && editor().isFocused ? editor().getAttributes('heading').level : null"
    :list="false"
>
    <x-filament-tiptap-editor::button
        action="editor().chain().focus().toggleHeading({level: 1}).run()"
        icon="heading-one"
        :secondary="true"
        label="{{ trans('filament-tiptap-editor::editor.heading.label') }} 1"
    />

    <x-filament-tiptap-editor::button
        action="editor().chain().focus().toggleHeading({level: 2}).run()"
        icon="heading-two"
        :secondary="true"
        label="{{ trans('filament-tiptap-editor::editor.heading.label') }} 2"
    />

    <x-filament-tiptap-editor::button
        action="editor().chain().focus().toggleHeading({level: 3}).run()"
        icon="heading-three"
        :secondary="true"
        label="{{ trans('filament-tiptap-editor::editor.heading.label') }} 3"
    />

    <x-filament-tiptap-editor::button
        action="editor().chain().focus().toggleHeading({level: 4}).run()"
        icon="heading-four"
        :secondary="true"
        label="{{ trans('filament-tiptap-editor::editor.heading.label') }} 4"
    />

    <x-filament-tiptap-editor::button
        action="editor().chain().focus().toggleHeading({level: 5}).run()"
        icon="heading-five"
        :secondary="true"
        label="{{ trans('filament-tiptap-editor::editor.heading.label') }} 5"
    />

    <x-filament-tiptap-editor::button
        action="editor().chain().focus().toggleHeading({level: 6}).run()"
        icon="heading-six"
        :secondary="true"
        label="{{ trans('filament-tiptap-editor::editor.heading.label') }} 6"
    />

</x-filament-tiptap-editor::dropdown-button>
