@php
    $isDisabled = $isDisabled();
@endphp

<div
    x-data="{

        state: $wire.$entangle(@js($getStatePath())),

        availablePermissions: @js($getPermissions()),

        visiblePermissionGroups: [],

        init: function () {
            this.visiblePermissionGroups = Object.keys(this.availablePermissions)
                .filter((group) => Object.values(this.availablePermissions[group]).some(
                    (permission) => this.state.includes(permission)
                ))
        },

        addGroup: function (group) {
            this.visiblePermissionGroups.push(group)
        },

        removeGroup: function (group) {
            this.deselectPermissionsInGroup(group)

            this.visiblePermissionGroups = this.visiblePermissionGroups.filter((visibleGroup) => visibleGroup !== group)
        },

        deselectPermissionsInGroup: function (group) {
            const permissions = Object.values(this.availablePermissions[group])

            this.state = this.state.filter((permission) => ! permissions.includes(permission))
        },

    }"
    wire:key="{{ $getKey() }}.{{ $getGuard() }}"
    class="grid gap-3"
>
    @if (! $isDisabled)
        <div class="flex items-center justify-end">
            <x-filament::input.wrapper>
                <x-filament::input.select
                    x-bind:disabled="Object.keys(availablePermissions).length === visiblePermissionGroups.length"
                    x-on:change="addGroup($event.target.value); $event.target.value = ''"
                >
                    <option value="">Add permission</option>
                    <template x-for="group in Object.keys(availablePermissions).filter((group) => ! visiblePermissionGroups.includes(group)).sort()" x-bind:key="group">
                        <option x-bind:value="group" x-text="group"></option>
                    </template>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    @endif

    <div class="rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 divide-y divide-gray-950/5 dark:divide-white/10 grid">
        <div class="lg:flex lg:divide-x lg:divide-y-0 divide-gray-950/5 dark:divide-white/10">
            <div class="px-3 py-2 font-medium text-gray-950 dark:text-white lg:flex-1">
                Permissions
            </div>

            <div class="hidden text-sm divide-x lg:grid lg:grid-cols-6 lg:gap-0 divide-gray-950/5 dark:divide-white/10">
                @foreach ([
                    'view-any' => 'View',
                    'create' => 'Create',
                    'update' => 'Update',
                    'delete' => 'Delete',
                    'force-delete' => 'Force Delete',
                    'restore' => 'Restore',
                ] as $operation => $operationLabel)
                    <div class="flex items-center justify-center text-gray-950 dark:text-white px-3 py-2 lg:w-28">
                        {{ $operationLabel }}
                    </div>
                @endforeach
            </div>
        </div>

        <template x-for="group in visiblePermissionGroups" x-bind:key="group">
            <div class="flex flex-col divide-y lg:divide-x lg:divide-y-0 lg:flex-row divide-gray-950/5 dark:divide-white/10">
                <div class="group flex items-center justify-between gap-3 px-3 py-2 lg:flex-1">
                    <div x-text="group" class="text-sm text-gray-950 dark:text-white"></div>

                    @if (! $isDisabled)
                        <x-filament::link
                            x-on:click="removeGroup(group)"
                            color="danger"
                            size="xs"
                            tag="button"
                            class="transition lg:opacity-0 group-hover:opacity-100"
                        >
                            Remove
                        </x-filament::link>
                    @endif
                </div>
            
                <div class="px-3 py-2 grid grid-cols-2 md:grid-cols-3 lg:divide-x divide-gray-950/5 dark:divide-white/10 lg:px-0 lg:py-0 lg:grid-cols-6 lg:gap-0 gap-1 text-sm">
                    @foreach ([
                        'view-any' => 'View',
                        'create' => 'Create',
                        'update' => 'Update',
                        'delete' => 'Delete',
                        'force-delete' => 'Force Delete',
                        'restore' => 'Restore',
                    ] as $operation => $operationLabel)
                        <label
                            @if ($operation !== 'view-any')
                                x-bind:class="{
                                    'opacity-50': ! state.includes(availablePermissions[group]['view-any']),
                                    'hidden' => ! Object.keys(availablePermissions[group]).includes(@js($operation)),
                                }"
                            @endif
                            class="flex items-center gap-2 lg:flex lg:px-3 lg:py-2 lg:justify-center lg:w-28"
                        >
                            <x-filament::input.checkbox
                                x-model="state"
                                :x-bind:value="'availablePermissions[group][' . \Illuminate\Support\Js::from($operation) . ']'"
                                :x-bind:disabled="(($operation === 'view-any') || $isDisabled) ? null : '! state.includes(availablePermissions[group][\'view-any\'])'"
                                :x-on:change="(($operation === 'view-any') && (! $isDisabled)) ? '$event.target.checked ? (availablePermissions[group].view ? state.push(availablePermissions[group].view) : null) : deselectPermissionsInGroup(group)' : null"
                                :disabled="$isDisabled"
                                :x-show="'Object.keys(availablePermissions[group]).includes(' . \Illuminate\Support\Js::from($operation) . ')'"
                            />
        
                            <span class="lg:sr-only">
                                {{ $operationLabel }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </template>

        <div x-show="! visiblePermissionGroups.length" class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400">
            No permissions added.
        </div>
    </div>
</div>