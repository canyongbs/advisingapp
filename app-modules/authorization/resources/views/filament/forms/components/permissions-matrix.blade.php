{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.
    
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
    use Illuminate\Support\Js;

    $isDisabled = $isDisabled();
@endphp

<div
    class="grid gap-3"
    x-data="{
        state: $wire.$entangle(@js($getStatePath())),

        availablePermissions: @js($getAvailablePermissions()),

        visiblePermissionGroups: [],

        init: function () {
            this.visiblePermissionGroups = Object.keys(
                this.availablePermissions,
            ).filter((group) =>
                Object.values(this.availablePermissions[group]).some((permission) =>
                    this.state.includes(permission),
                ),
            )
        },

        addGroup: function (group) {
            this.visiblePermissionGroups.push(group)
        },

        removeGroup: function (group) {
            this.deselectPermissionsInGroup(group)

            this.visiblePermissionGroups = this.visiblePermissionGroups.filter(
                (visibleGroup) => visibleGroup !== group,
            )
        },

        deselectPermissionsInGroup: function (group) {
            const permissions = Object.values(this.availablePermissions[group])

            this.state = this.state.filter(
                (permission) => ! permissions.includes(permission),
            )
        },

        selectAllInColumn: function (column) {
            $root
                .querySelectorAll(
                    `input[type='checkbox'][data-column='${column}']:not([disabled]):not([style*='display: none']):not(:checked)`,
                )
                .forEach((checkbox) => {
                    if (checkbox.checked) {
                        return
                    }

                    checkbox.click()
                })
        },

        selectNoneInColumn: function (column) {
            $root
                .querySelectorAll(
                    `input[type='checkbox'][data-column='${column}']:not([disabled]):not([style*='display: none']):checked`,
                )
                .forEach((checkbox) => {
                    if (! checkbox.checked) {
                        return
                    }

                    checkbox.click()
                })
        },
    }"
    wire:key="{{ $getKey() }}.{{ $getGuard() }}"
>
    @if (! $isDisabled)
        <div class="flex items-center justify-end">
            <x-filament::input.wrapper>
                <x-filament::input.select
                    x-bind:disabled="Object.keys(availablePermissions).length === visiblePermissionGroups.length"
                    x-on:change="addGroup($event.target.value); $event.target.value = ''"
                >
                    <option value="">Add permission</option>
                    <template
                        x-for="
                            group in
                                Object.keys(availablePermissions)
                                    .filter((group) => ! visiblePermissionGroups.includes(group))
                                    .sort()
                        "
                        x-bind:key="group"
                    >
                        <option x-bind:value="group" x-text="group"></option>
                    </template>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    @endif

    <div
        class="divide-gray-950/5 ring-gray-950/5 grid divide-y rounded-xl bg-white shadow-sm ring-1 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10"
        x-ref="table"
    >
        <div class="divide-gray-950/5 xl:flex xl:divide-x xl:divide-y-0 dark:divide-white/10">
            <div class="flex items-center px-3 py-2 font-medium text-gray-950 xl:flex-1 dark:text-white">
                Permissions
            </div>

            <div class="divide-gray-950/5 hidden divide-x text-xs xl:grid xl:grid-cols-7 xl:gap-0 dark:divide-white/10">
                @foreach (['View', 'Create', 'Update', 'Delete', 'Import', 'Force Delete', 'Restore'] as $operationLabel)
                    <div
                        class="flex flex-col items-center justify-center p-2 font-semibold text-gray-950 xl:w-24 dark:text-white"
                    >
                        <div>{{ $operationLabel }}</div>
                        {{-- prettier-ignore-start --}}
                        <div
                            class="flex items-center gap-2"
                            x-data="{ isVisible{{ $loop->index }}: false }"
                            x-init="setInterval(() => isVisible{{ $loop->index }} = $el.closest(`[x-ref=\'table\']`).querySelectorAll(`input[type='checkbox'][data-column='{{ $loop->index }}']:not([disabled]):not([style*='display: none'])`).length, 100)"
                            x-show="isVisible{{ $loop->index }}"
                            x-cloak
                        >
                            <x-filament::link
                                tag="button"
                                :x-on:click="'selectAllInColumn('.$loop->index.')'"
                                :x-data="'{ isAllVisible'.$loop->index.': false }'"
                                :x-init="'setInterval(() => isAllVisible'.$loop->index.' = $el.closest(`[x-ref=\'table\']`).querySelectorAll(`input[type=\'checkbox\'][data-column=\''.$loop->index.'\']:not([disabled]):not([style*=\'display: none\']):not(:checked)`).length, 100)'"
                                :x-show="'isAllVisible'.$loop->index"
                                x-cloak
                            >
                                <span class="text-xs">All</span>
                            </x-filament::link>

                            <x-filament::link
                                tag="button"
                                :x-on:click="'selectNoneInColumn('.$loop->index.')'"
                                :x-data="'{ isNoneVisible'.$loop->index.': false }'"
                                :x-init="'setInterval(() => isNoneVisible'.$loop->index.' = $el.closest(`[x-ref=\'table\']`).querySelectorAll(`input[type=\'checkbox\'][data-column=\''.$loop->index.'\']:not([disabled]):not([style*=\'display: none\']):checked`).length, 100)'"
                                :x-show="'isNoneVisible'.$loop->index"
                                x-cloak
                            >
                                <span class="text-xs">None</span>
                            </x-filament::link>
                        </div>
                        {{-- prettier-ignore-end --}}
                    </div>
                @endforeach
            </div>
        </div>

        <template x-for="group in visiblePermissionGroups.sort()" x-bind:key="group">
            <div
                class="divide-gray-950/5 flex flex-col divide-y xl:flex-row xl:divide-x xl:divide-y-0 dark:divide-white/10"
            >
                <div class="group flex items-center justify-between gap-3 px-3 py-2 xl:flex-1">
                    <div class="text-sm text-gray-950 dark:text-white" x-text="group"></div>

                    @if (! $isDisabled)
                        <x-filament::link
                            class="transition group-hover:opacity-100 xl:opacity-0"
                            x-on:click="removeGroup(group)"
                            color="danger"
                            size="xs"
                            tag="button"
                        >
                            Remove
                        </x-filament::link>
                    @endif
                </div>

                <div
                    class="divide-gray-950/5 grid grid-cols-2 gap-1 px-3 py-2 text-sm md:grid-cols-4 xl:grid-cols-7 xl:gap-0 xl:divide-x xl:px-0 xl:py-0 dark:divide-white/10"
                >
                    @foreach (['view-any' => 'View', 'create' => 'Create', 'update' => 'Update', 'delete' => 'Delete', 'import' => 'Import', 'force-delete' => 'Force Delete', 'restore' => 'Restore'] as $operation => $operationLabel)
                        <label
                            class="flex items-center gap-2 xl:flex xl:w-24 xl:justify-center xl:px-3 xl:py-2"
                            @if ($operation !== 'view-any') x-bind:class="{
                                'opacity-50': ! state.includes(availablePermissions[group]['view-any']),
                                'hidden': ! Object.keys(availablePermissions[group]).includes(@js($operation)),
                            }" @endif
                        >
                            <x-filament::input.checkbox
                                x-model="state"
                                :data-column="$loop->index"
                                :x-bind:value="'availablePermissions[group]['.Js::from($operation).
                                                                                                ']'"
                                :x-bind:disabled="(($operation === 'view-any') || $isDisabled) ? null:
                                    '! state.includes(availablePermissions[group][\'view-any\'])'"
                                :x-on:change="(($operation === 'view-any') && (! $isDisabled)) ? '$event.target.checked ? (availablePermissions[group].view ? state.push(availablePermissions[group].view) : null) : deselectPermissionsInGroup(group)' : null"
                                :disabled="$isDisabled"
                                :x-show="'Object.keys(availablePermissions[group]).includes(' .
                                    Js::from($operation) .
                                ')'"
                            />

                            <span class="xl:sr-only">
                                {{ $operationLabel }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </template>

        <div class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400" x-show="! visiblePermissionGroups.length">
            No permissions added.
        </div>
    </div>
</div>
