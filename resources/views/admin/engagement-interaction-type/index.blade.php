@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.engagementInteractionType.title_singular') }}
                    {{ trans('global.list') }}
                </h6>

                @can('engagement_interaction_type_create')
                    <a class="btn btn-indigo" href="{{ route('admin.engagement-interaction-types.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.engagementInteractionType.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('engagement-interaction-type.index')

    </div>
</div>
@endsection