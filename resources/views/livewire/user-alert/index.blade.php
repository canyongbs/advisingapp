<div>
    <div class="card-controls sm:flex">
        <div class="w-full sm:w-1/2">
            Per page:
            <select wire:model="perPage" class="form-select w-full sm:w-1/6">
                @foreach($paginationOptions as $value)
                    <option value="{{ $value }}">{{ $value }}</option>
                @endforeach
            </select>

            @can('user_alert_delete')
                <button class="btn btn-rose ml-3 disabled:opacity-50 disabled:cursor-not-allowed" type="button" wire:click="confirm('deleteSelected')" wire:loading.attr="disabled" {{ $this->selectedCount ? '' : 'disabled' }}>
                    {{ __('Delete Selected') }}
                </button>
            @endcan

            @if(file_exists(app_path('Http/Livewire/ExcelExport.php')))
                <livewire:excel-export model="UserAlert" format="csv" />
                <livewire:excel-export model="UserAlert" format="xlsx" />
                <livewire:excel-export model="UserAlert" format="pdf" />
            @endif




        </div>
        <div class="w-full sm:w-1/2 sm:text-right">
            Search:
            <input type="text" wire:model.debounce.300ms="search" class="w-full sm:w-1/3 inline-block" />
        </div>
    </div>
    <div wire:loading.delay>
        Loading...
    </div>

    <div class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table table-index w-full">
                <thead>
                    <tr>
                        <th class="w-9">
                        </th>
                        <th class="w-28">
                            {{ trans('cruds.userAlert.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.userAlert.fields.message') }}
                            @include('components.table.sort', ['field' => 'message'])
                        </th>
                        <th>
                            {{ trans('cruds.userAlert.fields.link') }}
                            @include('components.table.sort', ['field' => 'link'])
                        </th>
                        <th>
                            {{ trans('cruds.userAlert.fields.users') }}
                        </th>
                        <th>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userAlerts as $userAlert)
                        <tr>
                            <td>
                                <input type="checkbox" value="{{ $userAlert->id }}" wire:model="selected">
                            </td>
                            <td>
                                {{ $userAlert->id }}
                            </td>
                            <td>
                                {{ $userAlert->message }}
                            </td>
                            <td>
                                {{ $userAlert->link }}
                            </td>
                            <td>
                                @foreach($userAlert->users as $key => $entry)
                                    <span class="badge badge-relationship">{{ $entry->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('user_alert_show')
                                        <a class="btn btn-sm btn-info mr-2" href="{{ route('admin.user-alerts.show', $userAlert) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('user_alert_edit')
                                        <a class="btn btn-sm btn-success mr-2" href="{{ route('admin.user-alerts.edit', $userAlert) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('user_alert_delete')
                                        <button class="btn btn-sm btn-rose mr-2" type="button" wire:click="confirm('delete', {{ $userAlert->id }})" wire:loading.attr="disabled">
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
            @if($this->selectedCount)
                <p class="text-sm leading-5">
                    <span class="font-medium">
                        {{ $this->selectedCount }}
                    </span>
                    {{ __('Entries selected') }}
                </p>
            @endif
            {{ $userAlerts->links() }}
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