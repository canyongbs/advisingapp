@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.caseItemType.title_singular') }}
                    {{ trans('global.list') }}
                </h6>

                @can('case_item_type_create')
                    <a class="btn btn-indigo" href="{{ route('admin.case-item-types.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.caseItemType.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('case-item-type.index')

    </div>
</div>
@endsection