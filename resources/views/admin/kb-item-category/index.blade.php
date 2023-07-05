@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.kbItemCategory.title_singular') }}
                    {{ trans('global.list') }}
                </h6>

                @can('kb_item_category_create')
                    <a class="btn btn-indigo" href="{{ route('admin.kb-item-categories.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.kbItemCategory.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('kb-item-category.index')

    </div>
</div>
@endsection