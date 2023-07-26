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

            @can('kb_item_delete')
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
                    model="KbItem"
                />
                <livewire:excel-export
                    format="xlsx"
                    model="KbItem"
                />
                <livewire:excel-export
                    format="pdf"
                    model="KbItem"
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
                            {{ trans('cruds.kbItem.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.kbItem.fields.question') }}
                            @include('components.table.sort', ['field' => 'question'])
                        </th>
                        <th>
                            {{ trans('cruds.kbItem.fields.quality') }}
                            @include('components.table.sort', ['field' => 'quality.rating'])
                        </th>
                        <th>
                            {{ trans('cruds.kbItem.fields.status') }}
                            @include('components.table.sort', ['field' => 'status.status'])
                        </th>
                        <th>
                            {{ trans('cruds.kbItem.fields.public') }}
                            @include('components.table.sort', ['field' => 'public'])
                        </th>
                        <th>
                            {{ trans('cruds.kbItem.fields.category') }}
                            @include('components.table.sort', ['field' => 'category.category'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($kbItems as $kbItem)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $kbItem->id }}"
                                    wire:model.live="selected"
                                >
                            </td>
                            <td>
                                {{ $kbItem->id }}
                            </td>
                            <td>
                                {{ $kbItem->question }}
                            </td>
                            <td>
                                @if ($kbItem->quality)
                                    <span class="badge badge-relationship">{{ $kbItem->quality->rating ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($kbItem->status)
                                    <span class="badge badge-relationship">{{ $kbItem->status->status ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $kbItem->public_label }}
                            </td>
                            <td>
                                @if ($kbItem->category)
                                    <span
                                        class="badge badge-relationship">{{ $kbItem->category->category ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('kb_item_show')
                                        <a
                                            class="btn btn-sm btn-info mr-2"
                                            href="{{ route('admin.kb-items.show', $kbItem) }}"
                                        >
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('kb_item_edit')
                                        <a
                                            class="btn btn-sm btn-success mr-2"
                                            href="{{ route('admin.kb-items.edit', $kbItem) }}"
                                        >
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('kb_item_delete')
                                        <button
                                            class="btn btn-sm btn-rose mr-2"
                                            type="button"
                                            wire:click="confirm('delete', {{ $kbItem->id }})"
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
            {{ $kbItems->links() }}
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
