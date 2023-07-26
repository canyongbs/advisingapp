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

            @can('engagement_email_item_delete')
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
                    model="EngagementEmailItem"
                />
                <livewire:excel-export
                    format="xlsx"
                    model="EngagementEmailItem"
                />
                <livewire:excel-export
                    format="pdf"
                    model="EngagementEmailItem"
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
                            {{ trans('cruds.engagementEmailItem.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.engagementEmailItem.fields.email') }}
                            @include('components.table.sort', ['field' => 'email'])
                        </th>
                        <th>
                            {{ trans('cruds.engagementEmailItem.fields.subject') }}
                            @include('components.table.sort', ['field' => 'subject'])
                        </th>
                        <th>
                            {{ trans('cruds.engagementEmailItem.fields.body') }}
                            @include('components.table.sort', ['field' => 'body'])
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($engagementEmailItems as $engagementEmailItem)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $engagementEmailItem->id }}"
                                    wire:model.live="selected"
                                >
                            </td>
                            <td>
                                {{ $engagementEmailItem->id }}
                            </td>
                            <td>
                                <a
                                    class="link-light-blue"
                                    href="mailto:{{ $engagementEmailItem->email }}"
                                >
                                    <i class="far fa-envelope fa-fw">
                                    </i>
                                    {{ $engagementEmailItem->email }}
                                </a>
                            </td>
                            <td>
                                {{ $engagementEmailItem->subject }}
                            </td>
                            <td>
                                {{ $engagementEmailItem->body }}
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('engagement_email_item_show')
                                        <a
                                            class="btn btn-sm btn-info mr-2"
                                            href="{{ route('admin.engagement-email-items.show', $engagementEmailItem) }}"
                                        >
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('engagement_email_item_edit')
                                        <a
                                            class="btn btn-sm btn-success mr-2"
                                            href="{{ route('admin.engagement-email-items.edit', $engagementEmailItem) }}"
                                        >
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('engagement_email_item_delete')
                                        <button
                                            class="btn btn-sm btn-rose mr-2"
                                            type="button"
                                            wire:click="confirm('delete', {{ $engagementEmailItem->id }})"
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
            {{ $engagementEmailItems->links() }}
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
