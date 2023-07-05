@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.kbItemQuality.title_singular') }}
                    {{ trans('global.list') }}
                </h6>

                @can('kb_item_quality_create')
                    <a class="btn btn-indigo" href="{{ route('admin.kb-item-qualities.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.kbItemQuality.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('kb-item-quality.index')

    </div>
</div>
@endsection