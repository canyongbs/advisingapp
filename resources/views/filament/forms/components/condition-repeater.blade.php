<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    @php
        $containers = $getChildComponentContainers();

        $addAction = $getAction($getAddActionName());
        $addBetweenAction = $getAction($getAddBetweenActionName());
        $cloneAction = $getAction($getCloneActionName());
        $deleteAction = $getAction($getDeleteActionName());
        $moveDownAction = $getAction($getMoveDownActionName());
        $moveUpAction = $getAction($getMoveUpActionName());
        $reorderAction = $getAction($getReorderActionName());

        $isAddable = $isAddable();
        $isCloneable = $isCloneable();
        $isCollapsible = $isCollapsible();
        $isDeletable = $isDeletable();
        $isReorderableWithButtons = $isReorderableWithButtons();
        $isReorderableWithDragAndDrop = $isReorderableWithDragAndDrop();

        $statePath = $getStatePath();
    @endphp

    <div
        x-data="{}"
        {{
            $attributes
                ->merge($getExtraAttributes(), escape: false)
                ->class([
                    'fi-fo-repeater grid gap-y-4 relative',
                    'border-s-2 ps-6' => count($containers) > 1,
                ])
        }}
    >
        @if (count($containers) > 1)
            <div class="absolute -start-5 inset-y-auto -rotate-90 h-full flex items-center">
                <div class="bg-white font-medium px-2">
                    or
                </div>
            </div>
        @endif

        @if (count($containers))
            <ul class="flex flex-col gap-y-4">
                @foreach ($containers as $uuid => $item)
                    @php
                        $itemLabel = $getItemLabel($uuid);
                    @endphp

                    <li
                        wire:key="{{ $this->getId() }}.{{ $item->getStatePath() }}.{{ $field::class }}.item"
                        x-data="{
                            isCollapsed: @js($isCollapsed($item)),
                        }"
                        x-on:expand-concealing-component.window="
                            error = $el.querySelector('[data-validation-error]')

                            if (! error) {
                                return
                            }

                            isCollapsed = false

                            if (document.body.querySelector('[data-validation-error]') !== error) {
                                return
                            }

                            setTimeout(
                                () =>
                                    $el.scrollIntoView({
                                        behavior: 'smooth',
                                        block: 'start',
                                        inline: 'start',
                                    }),
                                200,
                            )
                        "
                        x-on:repeater-expand.window="$event.detail === '{{ $statePath }}' && (isCollapsed = false)"
                        x-on:repeater-collapse.window="$event.detail === '{{ $statePath }}' && (isCollapsed = true)"
                        x-sortable-item="{{ $uuid }}"
                        x-bind:class="isCollapsed && 'fi-collapsed'"
                        class="fi-fo-repeater-item border-s-2 flex items-start relative"
                    >
                        @if (count($item->getComponent('conditions')->getChildComponentContainers()) > 1)
                            <div x-show="! isCollapsed" class="absolute -start-6 inset-y-auto -rotate-90 h-full flex items-center">
                                <div class="bg-white font-medium px-2">
                                    and
                                </div>
                            </div>
                        @endif

                        <div
                            class="px-6 py-4 flex-1"
                            x-show="! isCollapsed"
                        >
                            {{ $item }}
                        </div>

                        @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons || filled($itemLabel) || $isCloneable || $isDeletable || $isCollapsible)
                            <div class="flex">
                                <div class="p-1 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-white/5 dark:ring-white/10">
                                    @if ($isReorderableWithDragAndDrop || $isReorderableWithButtons)
                                        <ul>
                                            @if ($isReorderableWithDragAndDrop)
                                                <li x-sortable-handle>
                                                    {{ $reorderAction }}
                                                </li>
                                            @endif

                                            @if ($isReorderableWithButtons)
                                                <li
                                                    class="flex items-center justify-center"
                                                >
                                                    {{ $moveUpAction(['item' => $uuid])->disabled($loop->first) }}
                                                </li>

                                                <li
                                                    class="flex items-center justify-center"
                                                >
                                                    {{ $moveDownAction(['item' => $uuid])->disabled($loop->last) }}
                                                </li>
                                            @endif
                                        </ul>
                                    @endif

                                    @if ($isCloneable || $isDeletable || $isCollapsible)
                                        <ul>
                                            @if ($isCloneable)
                                                <li>
                                                    {{ $cloneAction(['item' => $uuid]) }}
                                                </li>
                                            @endif

                                            @if ($isDeletable)
                                                <li>
                                                    {{ $deleteAction(['item' => $uuid]) }}
                                                </li>
                                            @endif

                                            @if ($isCollapsible)
                                                <li
                                                    class="relative transition"
                                                    x-on:click.stop="isCollapsed = !isCollapsed"
                                                    x-bind:class="{ '-rotate-180': isCollapsed }"
                                                >
                                                    <div
                                                        class="transition"
                                                        x-bind:class="{ 'opacity-0 pointer-events-none': isCollapsed }"
                                                    >
                                                        {{ $getAction('collapse') }}
                                                    </div>

                                                    <div
                                                        class="absolute inset-0 rotate-180 transition"
                                                        x-bind:class="{ 'opacity-0 pointer-events-none': ! isCollapsed }"
                                                    >
                                                        {{ $getAction('expand') }}
                                                    </div>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </li>

                    @if ((! $loop->last) && $isAddable && $addBetweenAction->isVisible())
                        <li class="flex">
                            <div class="rounded-lg bg-white dark:bg-gray-900">
                                {{ $addBetweenAction(['afterItem' => $uuid]) }}
                            </div>
                        </li>
                    @endif
                @endforeach
            </ul>
        @endif

        @if ($isAddable)
            <div>
                {{ $addAction }}
            </div>
        @endif
    </div>
</x-dynamic-component>
