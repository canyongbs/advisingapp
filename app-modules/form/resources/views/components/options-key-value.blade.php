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
    Adapted from Filament's KeyValue embedded view and Alpine component. Each row's
    "key" is the stored option value and its "value" is the label, but the label
    column is rendered first and the key is generated from it in the browser.
--}}
<div
    x-data="{
        state: $wire.{{ $entangleExpression }},

        rows: [],

        init() {
            this.updateRows()

            if (this.rows.length <= 0) {
                this.rows.push({ key: '', value: '' })
            } else {
                this.updateState()
            }

            this.$watch('state', (state, oldState) => {
                if (! Array.isArray(state)) {
                    return
                }

                if (
                    state.length === 0 &&
                    Array.isArray(oldState) &&
                    oldState.length === 0
                ) {
                    return
                }

                this.updateRows()
            })
        },

        slugify(label) {
            return (label ?? '')
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase()
                .replace(/[^\p{L}\p{N}]+/gu, '-')
                .replace(/^-+|-+$/g, '')
        },

        addRow() {
            this.rows.push({ key: '', value: '' })

            this.updateState()
        },

        deleteRow(index) {
            this.rows.splice(index, 1)

            if (this.rows.length <= 0) {
                this.addRow()
            }

            this.updateState()
        },

        reorderRows(event) {
            const rows = Alpine.raw(this.rows)

            this.rows = []

            const reorderedRow = rows.splice(event.oldIndex, 1)[0]
            rows.splice(event.newIndex, 0, reorderedRow)

            this.$nextTick(() => {
                this.rows = rows

                this.updateState()
            })
        },

        updateRows() {
            const mergedRows = Alpine.raw(this.state).map((row) => ({
                key: row.key,
                value: row.value,
            }))

            this.rows.forEach((row) => {
                if (row.value === '' || row.value === null) {
                    mergedRows.push({ key: '', value: '' })
                }
            })

            this.rows = mergedRows
        },

        updateState() {
            const state = this.rows
                .filter((row) => row.value !== '' && row.value !== null)
                .map((row) => ({ key: row.key, value: row.value }))

            if (JSON.stringify(this.state) !== JSON.stringify(state)) {
                this.state = state
            }
        },
    }"
    wire:ignore
    wire:key="{{ $livewireKey }}.{{ $isDisabled ? "disabled" : "enabled" }}"
    {!! $alpineAttributes !!}
>
    <table aria-labelledby="{{ $id }}-label" id="{{ $id }}" class="fi-fo-key-value-table">
        <thead>
            <tr>
                @if ($isReorderable && ! $isDisabled)
                    <th scope="col" x-show="rows.length" class="fi-has-action">
                        <span class="fi-sr-only">
                            {{ __("filament-forms::components.key_value.columns.reorder.label") }}
                        </span>
                    </th>
                @endif

                <th scope="col">{{ $valueLabel }}</th>

                <th scope="col">{{ $keyLabel }}</th>

                @if ($isDeletable && ! $isDisabled)
                    <th scope="col" x-show="rows.length" class="fi-has-action">
                        <span class="fi-sr-only">
                            {{ __("filament-forms::components.key_value.columns.actions.label") }}
                        </span>
                    </th>
                @endif
            </tr>
        </thead>

        <tbody
            @if ($isReorderable)
                x-on:end.stop="reorderRows($event)"
                x-sortable
                data-sortable-animation-duration="{{ $reorderAnimationDuration }}"
            @endif
        >
            <template x-bind:key="index" x-for="(row, index) in rows">
                <tr
                    @if ($isReorderable)
                        x-bind:x-sortable-item="row.key"
                    @endif
                >
                    @if ($isReorderable && ! $isDisabled)
                        <td class="fi-has-action">
                            <div x-sortable-handle class="fi-fo-key-value-table-row-sortable-handle">
                                {!! $reorderActionHtml !!}
                            </div>
                        </td>
                    @endif

                    <td>
                        <input
                            aria-label="{{ $valueLabel }}"
                            @disabled($isDisabled)
                            type="text"
                            x-model="row.value"
                            x-on:input="row.key = slugify(row.value)"
                            x-on:input.debounce.{{ $debounce }}="updateState"
                            class="fi-input"
                        />
                    </td>

                    <td>
                        <input
                            aria-label="{{ $keyLabel }}"
                            disabled
                            type="text"
                            x-model="row.key"
                            class="fi-input"
                        />
                    </td>

                    @if ($isDeletable && ! $isDisabled)
                        <td class="fi-has-action">
                            <div x-on:click="deleteRow(index)">
                                {!! $deleteActionHtml !!}
                            </div>
                        </td>
                    @endif
                </tr>
            </template>
        </tbody>
    </table>

    @if ($isAddable && ! $isDisabled)
        <div x-on:click="addRow" class="fi-fo-key-value-add-action-ctn">
            {!! $addActionHtml !!}
        </div>
    @endif
</div>
