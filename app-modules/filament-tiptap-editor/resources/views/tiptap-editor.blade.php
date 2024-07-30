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
@php
    $tools = $getTools();
    $bubbleMenuTools = $getBubbleMenuTools();
    $floatingMenuTools = $getFloatingMenuTools();
    $statePath = $getStatePath();
    $isDisabled = $isDisabled();
    $blocks = $getBlocks();
    $mergeTags = $getMergeTags();
    $shouldSupportBlocks = $shouldSupportBlocks();
    $shouldShowMergeTagsInBlocksPanel = $shouldShowMergeTagsInBlocksPanel();
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div class="flex gap-3">
        <div class="flex-1">
            <div
                @class([
                    'tiptap-editor rounded-md relative text-gray-950 bg-white shadow-sm ring-1 dark:bg-white/5 dark:text-white',
                    'ring-gray-950/10 dark:ring-white/20' => !$errors->has($statePath),
                    'ring-danger-600 dark:ring-danger-600' => $errors->has($statePath),
                ])
                x-data="{
                    isUploadingFile: false,
                }"
                x-bind:class="{
                    'pointer-events-none opacity-50 cursor-wait': isUploadingFile,
                }"
                x-on:tiptap-uploading-file.stop="if ($event.detail.statePath === @js($statePath)) isUploadingFile = true"
                x-on:tiptap-uploaded-file.stop="if ($event.detail.statePath === @js($statePath)) isUploadingFile = false"
                @if (!$shouldDisableStylesheet()) x-load-css="[@js(\Filament\Support\Facades\FilamentAsset::getStyleHref('tiptap', 'awcodes/tiptap-editor'))]" @endif
            >
                <div
                    class="tiptap-wrapper relative z-0 rounded-md bg-white focus-within:z-10 focus-within:ring focus-within:ring-primary-500 dark:bg-gray-900"
                    wire:ignore
                    x-ignore
                    ax-load="visible"
                    ax-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('tiptap', 'awcodes/tiptap-editor') }}"
                    x-bind:class="{ 'tiptap-fullscreen': fullScreenMode }"
                    x-data="tiptap({
                        state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')", isOptimisticallyLive: true) }},
                        statePath: '{{ $statePath }}',
                        tools: @js($tools),
                        disabled: @js($isDisabled),
                        locale: '{{ app()->getLocale() }}',
                        floatingMenuTools: @js($floatingMenuTools),
                        placeholder: @js($getPlaceholder()),
                        mergeTags: @js($mergeTags),
                        uploadFileUrl: @js(url()->signedRoute('livewire.upload-file')),
                        uploadingMessage: @js(__('filament::components/button.messages.uploading_file')),
                    })"
                    x-init="$nextTick(() => { init() })"
                    x-on:click.away="blur()"
                    x-on:keydown.escape="fullScreenMode = false"
                    x-on:insert-content.window="insertContent($event)"
                    x-on:unset-link.window="$event.detail.statePath === '{{ $statePath }}' ? unsetLink() : null"
                    x-on:update-editor-content.window="updateEditorContent($event)"
                    x-on:refresh-tiptap-editors.window="refreshEditorContent()"
                    x-on:dragged-block.stop="$wire.mountFormComponentAction('{{ $statePath }}', 'insertBlock', {
                        type: $event.detail.type,
                        coordinates: $event.detail.coordinates,
                    })"
                    x-on:dragged-merge-tag.stop="insertMergeTag($event)"
                    x-on:insert-block.window="insertBlock($event)"
                    x-on:update-block.window="updateBlock($event)"
                    x-on:open-block-settings.window="openBlockSettings($event)"
                    x-on:delete-block.window="deleteBlock()"
                    x-on:open-modal.window="handleOpenModal()"
                    x-on:locale-change.window="updateLocale($event)"
                    x-trap.noscroll="fullScreenMode"
                >
                    @if (!$isDisabled && !$isToolbarMenusDisabled() && $tools)
                        <template x-if="editor()">
                            <div>
                                <button
                                    class="sr-only z-20 rounded focus:not-sr-only focus:absolute focus:bg-white focus:px-3 focus:py-1 focus:text-gray-900"
                                    type="button"
                                    x-on:click="editor().chain().focus()"
                                >{{ trans('filament-tiptap-editor::editor.skip_toolbar') }}</button>

                                <div
                                    class="tiptap-toolbar relative z-[1] flex flex-col divide-x divide-gray-950/10 rounded-t-md border-b border-gray-950/10 bg-gray-50 text-gray-800 dark:divide-white/20 dark:border-white/20 dark:bg-gray-950 dark:text-gray-300 md:flex-row">

                                    <div class="tiptap-toolbar-left flex flex-1 flex-wrap items-center gap-1 p-1">
                                        <x-dynamic-component
                                            component="filament-tiptap-editor::tools.paragraph"
                                            :state-path="$statePath"
                                        />
                                        @foreach ($tools as $tool)
                                            @if ($tool === '|')
                                                <div class="h-5 border-l border-gray-950/10 dark:border-white/20"></div>
                                            @elseif (is_array($tool))
                                                <x-dynamic-component
                                                    component="{{ $tool['button'] }}"
                                                    :state-path="$statePath"
                                                />
                                            @elseif ($tool === 'blocks')
                                                @if ($blocks && $shouldSupportBlocks)
                                                    <x-filament-tiptap-editor::tools.blocks
                                                        :blocks="$blocks"
                                                        :state-path="$statePath"
                                                    />
                                                @endif
                                            @else
                                                <x-dynamic-component
                                                    component="filament-tiptap-editor::tools.{{ $tool }}"
                                                    :state-path="$statePath"
                                                    :editor="$field"
                                                />
                                            @endif
                                        @endforeach
                                    </div>

                                    <div
                                        class="tiptap-toolbar-right flex flex-wrap items-start gap-1 self-stretch p-1 pl-2">
                                        <x-filament-tiptap-editor::tools.undo />
                                        <x-filament-tiptap-editor::tools.redo />
                                        <x-filament-tiptap-editor::tools.erase />
                                        <x-filament-tiptap-editor::tools.fullscreen />
                                    </div>
                                </div>
                            </div>
                        </template>
                    @endif

                    @if (!$isBubbleMenusDisabled())
                        <template x-if="editor()">
                            <div>
                                <div
                                    class="tiptap-editor-bubble-menu-wrapper"
                                    x-ref="bubbleMenu"
                                >
                                    <x-filament-tiptap-editor::menus.default-bubble-menu
                                        :state-path="$statePath"
                                        :tools="$bubbleMenuTools"
                                    />
                                    <x-filament-tiptap-editor::menus.link-bubble-menu
                                        :state-path="$statePath"
                                        :tools="$tools"
                                    />
                                    <x-filament-tiptap-editor::menus.image-bubble-menu
                                        :state-path="$statePath"
                                        :tools="$tools"
                                    />
                                    <x-filament-tiptap-editor::menus.table-bubble-menu
                                        :state-path="$statePath"
                                        :tools="$tools"
                                    />
                                </div>
                            </div>
                        </template>
                    @endif

                    @if (!$isFloatingMenusDisabled() && filled($floatingMenuTools))
                        <template x-if="editor()">
                            <div>
                                <div
                                    class="tiptap-editor-floating-menu-wrapper"
                                    x-ref="floatingMenu"
                                >
                                    <x-filament-tiptap-editor::menus.default-floating-menu
                                        :state-path="$statePath"
                                        :tools="$floatingMenuTools"
                                        :blocks="$blocks"
                                        :should-support-blocks="$shouldSupportBlocks"
                                        :editor="$field"
                                    />
                                </div>
                            </div>
                        </template>
                    @endif

                    <div class="flex h-full">
                        <div @class([
                            'tiptap-prosemirror-wrapper mx-auto w-full max-h-[40rem] min-h-[56px] h-auto overflow-y-scroll overflow-x-hidden rounded-b-md',
                            match ($getMaxContentWidth()) {
                                'sm' => 'prosemirror-w-sm',
                                'md' => 'prosemirror-w-md',
                                'lg' => 'prosemirror-w-lg',
                                'xl' => 'prosemirror-w-xl',
                                '2xl' => 'prosemirror-w-2xl',
                                '3xl' => 'prosemirror-w-3xl',
                                '4xl' => 'prosemirror-w-4xl',
                                '6xl' => 'prosemirror-w-6xl',
                                '7xl' => 'prosemirror-w-7xl',
                                'full' => 'prosemirror-w-none',
                                default => 'prosemirror-w-5xl',
                            },
                        ])>
                            <div
                                x-ref="element"
                                {{ $getExtraInputAttributeBag()->class(['tiptap-content min-h-full']) }}
                            ></div>
                        </div>

                        @if (!$isDisabled && ($shouldSupportBlocks || ($shouldShowMergeTagsInBlocksPanel && filled($mergeTags))))
                            <div
                                class="hidden h-full max-w-sm shrink-0 flex-col space-y-2 md:flex"
                                x-data="{
                                    isCollapsed: @js($shouldCollapseBlocksPanel()),
                                }"
                                x-bind:class="{
                                    'bg-gray-50 dark:bg-gray-950/20': !isCollapsed,
                                    'h-full': !isCollapsed && fullScreenMode,
                                    'px-2': !fullScreenMode,
                                    'px-3': fullScreenMode
                                }"
                            >
                                <div class="mt-2 flex items-center">
                                    <p
                                        class="text-xs font-bold"
                                        x-show="! isCollapsed"
                                    >
                                        @if ($shouldSupportBlocks)
                                            {{ trans('filament-tiptap-editor::editor.blocks.panel') }}
                                        @else
                                            {{ trans('filament-tiptap-editor::editor.merge_tags.panel') }}
                                        @endif
                                    </p>

                                    <button
                                        class="ml-auto"
                                        type="button"
                                        x-on:click="isCollapsed = false"
                                        x-show="isCollapsed"
                                        x-cloak
                                    >
                                        <x-filament::icon
                                            class="h-5 w-5"
                                            icon="heroicon-m-bars-3"
                                        />
                                    </button>

                                    <button
                                        class="ml-auto"
                                        type="button"
                                        x-on:click="isCollapsed = true"
                                        x-show="! isCollapsed"
                                    >
                                        <x-filament::icon
                                            class="h-5 w-5"
                                            icon="heroicon-m-x-mark"
                                        />
                                    </button>
                                </div>

                                <div
                                    class="h-full space-y-1 overflow-y-auto pb-2"
                                    x-show="! isCollapsed"
                                >
                                    @if ($shouldShowMergeTagsInBlocksPanel)
                                        @foreach ($mergeTags as $mergeTag)
                                            <div
                                                class="grid-col-1 flex cursor-move items-center gap-2 rounded border bg-white px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-800"
                                                draggable="true"
                                                x-on:dragstart="$event?.dataTransfer?.setData('mergeTag', @js($mergeTag))"
                                            >
                                                &lcub;&lcub; {{ $mergeTag }} &rcub;&rcub;
                                            </div>
                                        @endforeach
                                    @endif

                                    @foreach ($blocks as $block)
                                        <div
                                            class="grid-col-1 flex cursor-move items-center gap-2 rounded border bg-white px-3 py-2 text-xs dark:border-gray-700 dark:bg-gray-800"
                                            draggable="true"
                                            x-on:dragstart="$event?.dataTransfer?.setData('block', @js($block->getIdentifier()))"
                                        >
                                            @if ($block->getIcon())
                                                <x-filament::icon
                                                    class="h-5 w-5"
                                                    :icon="$block->getIcon()"
                                                />
                                            @endif

                                            {{ $block->getLabel() }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
