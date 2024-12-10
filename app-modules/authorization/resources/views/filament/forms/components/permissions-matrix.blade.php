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
    use Illuminate\Support\Js;

    $isDisabled = $isDisabled();
@endphp

<div
    class="grid gap-3"
    x-data="{
    
        state: $wire.$entangle(@js($getStatePath())),
    
        availablePermissions: @js($getAvailablePermissions()),
    
        visiblePermissionGroups: [],
    
        init: function() {
            this.visiblePermissionGroups = Object.keys(this.availablePermissions)
                .filter((group) => Object.values(this.availablePermissions[group]).some(
                    (permission) => this.state.includes(permission)
                ))
        },
    
        addGroup: function(group) {
            this.visiblePermissionGroups.push(group)
        },
    
        removeGroup: function(group) {
            this.deselectPermissionsInGroup(group)
    
            this.visiblePermissionGroups = this.visiblePermissionGroups.filter((visibleGroup) => visibleGroup !== group)
        },
    
        deselectPermissionsInGroup: function(group) {
            const permissions = Object.values(this.availablePermissions[group])
    
            this.state = this.state.filter((permission) => !permissions.includes(permission))
        },
    
    }"
    wire:key="{{ $getKey() }}.{{ $getGuard() }}"
>
    @if (!$isDisabled)
        <div class="flex items-center justify-end">
            <x-filament::input.wrapper>
                <x-filament::input.select
                    x-bind:disabled="Object.keys(availablePermissions).length === visiblePermissionGroups.length"
                    x-on:change="addGroup($event.target.value); $event.target.value = ''"
                >
                    <option value="">Add permission</option>
                    <template
                        x-for="group in Object.keys(availablePermissions).filter((group) => ! visiblePermissionGroups.includes(group)).sort()"
                        x-bind:key="group"
                    >
                        <option
                            x-bind:value="group"
                            x-text="group"
                        ></option>
                    </template>
                </x-filament::input.select>
            </x-filament::input.wrapper>
        </div>
    @endif

    <div
        class="grid divide-y divide-gray-950/5 rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
        <div class="divide-gray-950/5 dark:divide-white/10 lg:flex lg:divide-x lg:divide-y-0">
            <div class="px-3 py-2 font-medium text-gray-950 dark:text-white lg:flex-1">
                Permissions
            </div>

            <div class="hidden divide-x divide-gray-950/5 text-sm dark:divide-white/10 lg:grid lg:grid-cols-6 lg:gap-0">
                @foreach (['View', 'Create', 'Update', 'Delete', 'Force Delete', 'Restore'] as $operationLabel)
                    <div class="flex items-center justify-center px-3 py-2 text-gray-950 dark:text-white lg:w-28">
                        {{ $operationLabel }}
                    </div>
                @endforeach
            </div>
        </div>

        <template
            x-for="group in visiblePermissionGroups"
            x-bind:key="group"
        >
            <div
                class="flex flex-col divide-y divide-gray-950/5 dark:divide-white/10 lg:flex-row lg:divide-x lg:divide-y-0">
                <div class="group flex items-center justify-between gap-3 px-3 py-2 lg:flex-1">
                    <div
                        class="text-sm text-gray-950 dark:text-white"
                        x-text="group"
                    ></div>

                    @if (!$isDisabled)
                        <x-filament::link
                            class="transition group-hover:opacity-100 lg:opacity-0"
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
                    class="grid grid-cols-2 gap-1 divide-gray-950/5 px-3 py-2 text-sm dark:divide-white/10 md:grid-cols-3 lg:grid-cols-6 lg:gap-0 lg:divide-x lg:px-0 lg:py-0">
                    @foreach (['view-any' => 'View', 'create' => 'Create', 'update' => 'Update', 'delete' => 'Delete', 'force-delete' => 'Force Delete', 'restore' => 'Restore'] as $operation => $operationLabel)
                        <label
                            class="flex items-center gap-2 lg:flex lg:w-28 lg:justify-center lg:px-3 lg:py-2"
                            @if ($operation !== 'view-any') x-bind:class="{
                                    'opacity-50': ! state.includes(availablePermissions[group]['view-any']),
                                    'hidden': ! Object.keys(availablePermissions[group]).includes(@js($operation)),
                                }" @endif
                        >
                            <x-filament::input.checkbox
                                x-model="state"
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

                            <span class="lg:sr-only">
                                {{ $operationLabel }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </template>

        <div
            class="px-3 py-2 text-sm text-gray-500 dark:text-gray-400"
            x-show="! visiblePermissionGroups.length"
        >
            No permissions added.
        </div>
    </div>
</div>
