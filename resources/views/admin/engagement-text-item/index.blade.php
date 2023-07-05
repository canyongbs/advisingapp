@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.engagementTextItem.title_singular') }}
                    {{ trans('global.list') }}
                </h6>

                @can('engagement_text_item_create')
                    <a class="btn btn-indigo" href="{{ route('admin.engagement-text-items.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.engagementTextItem.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('engagement-text-item.index')

    </div>
</div>
@endsection