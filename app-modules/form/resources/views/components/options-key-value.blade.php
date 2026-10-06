{{--
    <COPYRIGHT>
    
    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.
    
    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
    same in return. Canyon GBS® and Advising App® are registered trademarks of
    Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
    vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
    Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
    in the Elastic License 2.0.
    
    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.
    
    </COPYRIGHT>
--}}
{{--
    Adapted from Filament's KeyValue embedded view, reusing its Alpine component. Each row's
    "key" is the stored option value and its "value" is the label, but the label
    column is rendered first and the key is generated from it in the browser.
--}}
@php
    $id = $getId();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field" label-tag="div" class="fi-fo-key-value-wrp">
    <x-filament::input.wrapper :valid="! $errors->has($getStatePath())" class="fi-fo-key-value">
        <div
            x-load
            x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('key-value', 'filament/forms') }}"
            x-data="keyValueFormComponent({
                        state: $wire.{{ $applyStateBindingModifiers("\$entangle('{$getStatePath()}')") }},
                    })"
            wire:ignore
            wire:key="{{ $getLivewireKey() }}"
            {{ $getExtraAlpineAttributeBag()->class(['fi-fo-key-value-table-ctn']) }}
        >
            <table
                aria-labelledby="{{ $id }}-label"
                id="{{ $id }}"
                class="fi-fo-key-value-table"
                x-data="{
                    slugify(label) {
                        return (label ?? '')
                            .normalize('NFD')
                            .replace(/[\u0300-\u036f]/g, '')
                            .toLowerCase()
                            .replace(/[^\p{L}\p{N}]+/gu, '-')
                            .replace(/^-+|-+$/g, '')
                    },
                }"
                x-init="
                    rows.forEach((row) => (row.key = slugify(row.value)))
                    updateState()
                "
            >
                <thead>
                    <tr>
                        <th scope="col" class="fi-has-action">
                            <span class="fi-sr-only">
                                {{ __('filament-forms::components.key_value.columns.reorder.label') }}
                            </span>
                        </th>

                        <th scope="col">Label</th>

                        <th scope="col">Value</th>

                        <th scope="col" class="fi-has-action">
                            <span class="fi-sr-only">
                                {{ __('filament-forms::components.key_value.columns.actions.label') }}
                            </span>
                        </th>
                    </tr>
                </thead>

                <tbody
                    x-on:end.stop="reorderRows($event)"
                    x-sortable
                    data-sortable-animation-duration="{{ $getReorderAnimationDuration() }}"
                >
                    <template x-bind:key="index" x-for="(row, index) in rows">
                        <tr x-bind:x-sortable-item="row.key">
                            <td class="fi-has-action">
                                <div x-sortable-handle class="fi-fo-key-value-table-row-sortable-handle">
                                    {{ $getAction('reorder') }}
                                </div>
                            </td>

                            <td>
                                <input
                                    aria-label="Label"
                                    type="text"
                                    x-model="row.value"
                                    x-on:input="row.key = slugify(row.value)"
                                    x-on:input.debounce.500ms="updateState"
                                    class="fi-input"
                                />
                            </td>

                            <td>
                                <input aria-label="Value" disabled type="text" x-model="row.key" class="fi-input" />
                            </td>

                            <td class="fi-has-action">
                                <div x-on:click="deleteRow(index)">
                                    {{ $getAction('delete') }}
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>

            <div x-on:click="addRow" class="fi-fo-key-value-add-action-ctn">
                {{ $getAction('add') }}
            </div>
        </div>
    </x-filament::input.wrapper>
</x-dynamic-component>
