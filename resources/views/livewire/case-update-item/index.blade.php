<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2">
            Per page:
            <select
                class="form-select w-full sm:w-1/6"
                wire:model.live="perPage"
            >
                @foreach ($paginationOptions as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </select>

            @can('case_update_item_delete')
                <button
                    class="btn btn-rose ml-3 disabled:cursor-not-allowed disabled:opacity-50"
                    type="button"
                    wire:click="confirm('deleteSelected')"
                    wire:loading.attr="disabled"
                    {{ $this->selectedCount ? '' : 'disabled' }}
                >
                    {{ __('Delete Selected') }}
                </button>
            @endcan

            @if (file_exists(app_path('Http/Livewire/ExcelExport.php')))
                <livewire:excel-export
                    format="csv"
                    model="CaseUpdateItem"
                />
                <livewire:excel-export
                    format="xlsx"
                    model="CaseUpdateItem"
                />
                <livewire:excel-export
                    format="pdf"
                    model="CaseUpdateItem"
                />
            @endif

        </div>
        <div class="w-full sm:w-1/2 sm:text-right">
            Search:
            <input
                class="inline-block w-full sm:w-1/3"
                type="text"
                wire:model.live.debounce.300ms="search"
            />
        </div>
    </div>
    <div wire:loading.delay>
        Loading...
    </div>

    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table-index table w-full">
                <thead>
                    <tr>
                        <th class="w-9">
                        </th>
                        <th class="w-28">
                            {{ trans('cruds.caseUpdateItem.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.caseUpdateItem.fields.student') }}
                            @include('components.table.sort', ['field' => 'student.full'])
                        </th>
                        <th>
                            {{ trans('cruds.recordStudentItem.fields.sisid') }}
                            @include('components.table.sort', ['field' => 'student.sisid'])
                        </th>
                        <th>
                            {{ trans('cruds.recordStudentItem.fields.otherid') }}
                            @include('components.table.sort', ['field' => 'student.otherid'])
                        </th>
                        <th>
                            {{ trans('cruds.caseUpdateItem.fields.case') }}
                            @include('components.table.sort', ['field' => 'case.casenumber'])
                        </th>
                        <th>
                            {{ trans('cruds.caseUpdateItem.fields.internal') }}
                            @include('components.table.sort', ['field' => 'internal'])
                        </th>
                        <th>
                            {{ trans('cruds.caseUpdateItem.fields.direction') }}
                            @include('components.table.sort', ['field' => 'direction'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($caseUpdateItems as $caseUpdateItem)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $caseUpdateItem->id }}"
                                    wire:model.live="selected"
                                >
                            </td>
                            <td>
                                {{ $caseUpdateItem->id }}
                            </td>
                            <td>
                                @if ($caseUpdateItem->student)
                                    <span
                                        class="badge badge-relationship">{{ $caseUpdateItem->student->full ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($caseUpdateItem->student)
                                    {{ $caseUpdateItem->student->sisid ?? '' }}
                                @endif
                            </td>
                            <td>
                                @if ($caseUpdateItem->student)
                                    {{ $caseUpdateItem->student->otherid ?? '' }}
                                @endif
                            </td>
                            <td>
                                @if ($caseUpdateItem->case)
                                    <span
                                        class="badge badge-relationship">{{ $caseUpdateItem->case->casenumber ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $caseUpdateItem->internal_label }}
                            </td>
                            <td>
                                {{ $caseUpdateItem->direction_label }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('case_update_item_show')
                                        <a
                                            class="btn btn-sm btn-info mr-2"
                                            href="{{ route('admin.case-update-items.show', $caseUpdateItem) }}"
                                        >
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('case_update_item_edit')
                                        <a
                                            class="btn btn-sm btn-success mr-2"
                                            href="{{ route('admin.case-update-items.edit', $caseUpdateItem) }}"
                                        >
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('case_update_item_delete')
                                        <button
                                            class="btn btn-sm btn-rose mr-2"
                                            type="button"
                                            wire:click="confirm('delete', {{ $caseUpdateItem->id }})"
                                            wire:loading.attr="disabled"
                                        >
                                            {{ trans('global.delete') }}
                                        </button>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10">No entries found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card-body">
        <div class="pt-3">
            @if ($this->selectedCount)
                <p class="text-sm leading-5">
                    <span class="font-medium">
                        {{ $this->selectedCount }}
                    </span>
                    {{ __('Entries selected') }}
                </p>
            @endif
            {{ $caseUpdateItems->links() }}
        </div>
    </div>
</div>

@push('scripts')
    <script>
        Livewire.on('confirm', e => {
            if (!confirm("{{ trans('global.areYouSure') }}")) {
                return
            }
            @this[e.callback](...e.argv)
        })
    </script>
@endpush
