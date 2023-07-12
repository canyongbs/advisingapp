@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-white">
            <div class="card-header border-b border-blueGray-200">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('cruds.caseItemStatus.title_singular') }}
                        {{ trans('global.list') }}
                    </h6>

                    @can('case_item_status_create')
                        <a
                            class="btn btn-indigo"
                            href="{{ route('admin.case-item-statuses.create') }}"
                        >
                            {{ trans('global.add') }} {{ trans('cruds.caseItemStatus.title_singular') }}
                        </a>
                    @endcan
                </div>
            </div>
            @livewire('case-item-status.index')

        </div>
    </div>
@endsection
