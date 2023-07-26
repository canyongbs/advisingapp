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

            @can('engagement_student_file_delete')
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
                    model="EngagementStudentFile"
                />
                <livewire:excel-export
                    format="xlsx"
                    model="EngagementStudentFile"
                />
                <livewire:excel-export
                    format="pdf"
                    model="EngagementStudentFile"
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
                            {{ trans('cruds.engagementStudentFile.fields.id') }}
                            @include('components.table.sort', ['field' => 'id'])
                        </th>
                        <th>
                            {{ trans('cruds.engagementStudentFile.fields.file') }}
                        </th>
                        <th>
                            {{ trans('cruds.engagementStudentFile.fields.description') }}
                            @include('components.table.sort', ['field' => 'description'])
                        </th>
                        <th>
                            {{ trans('cruds.engagementStudentFile.fields.student') }}
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
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($engagementStudentFiles as $engagementStudentFile)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    value="{{ $engagementStudentFile->id }}"
                                    wire:model.live="selected"
                                >
                            </td>
                            <td>
                                {{ $engagementStudentFile->id }}
                            </td>
                            <td>
                                @foreach ($engagementStudentFile->file as $key => $entry)
                                    <a
                                        class="link-light-blue"
                                        href="{{ $entry['url'] }}"
                                    >
                                        <i class="far fa-file">
                                        </i>
                                        {{ $entry['file_name'] }}
                                    </a>
                                @endforeach
                            </td>
                            <td>
                                {{ $engagementStudentFile->description }}
                            </td>
                            <td>
                                @if ($engagementStudentFile->student)
                                    <span
                                        class="badge badge-relationship">{{ $engagementStudentFile->student->full ?? '' }}</span>
                                @endif
                            </td>
                            <td>
                                @if ($engagementStudentFile->student)
                                    {{ $engagementStudentFile->student->sisid ?? '' }}
                                @endif
                            </td>
                            <td>
                                @if ($engagementStudentFile->student)
                                    {{ $engagementStudentFile->student->otherid ?? '' }}
                                @endif
                            </td>
                            <td>
                                <div class="flex justify-end">
                                    @can('engagement_student_file_show')
                                        <a
                                            class="btn btn-sm btn-info mr-2"
                                            href="{{ route('admin.engagement-student-files.show', $engagementStudentFile) }}"
                                        >
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan
                                    @can('engagement_student_file_edit')
                                        <a
                                            class="btn btn-sm btn-success mr-2"
                                            href="{{ route('admin.engagement-student-files.edit', $engagementStudentFile) }}"
                                        >
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan
                                    @can('engagement_student_file_delete')
                                        <button
                                            class="btn btn-sm btn-rose mr-2"
                                            type="button"
                                            wire:click="confirm('delete', {{ $engagementStudentFile->id }})"
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
            {{ $engagementStudentFiles->links() }}
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
