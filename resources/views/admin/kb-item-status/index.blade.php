@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.kbItemStatus.title_singular') }}
                    {{ trans('global.list') }}
                </h6>

                @can('kb_item_status_create')
                    <a class="btn btn-indigo" href="{{ route('admin.kb-item-statuses.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.kbItemStatus.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('kb-item-status.index')

    </div>
</div>
@endsection