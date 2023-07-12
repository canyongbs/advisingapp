@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-white">
            <div class="card-header border-b border-blueGray-200">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('cruds.engagementInteractionRelation.title_singular') }}
                        {{ trans('global.list') }}
                    </h6>

                    @can('engagement_interaction_relation_create')
                        <a
                            class="btn btn-indigo"
                            href="{{ route('admin.engagement-interaction-relations.create') }}"
                        >
                            {{ trans('global.add') }} {{ trans('cruds.engagementInteractionRelation.title_singular') }}
                        </a>
                    @endcan
                </div>
            </div>
            @livewire('engagement-interaction-relation.index')

        </div>
    </div>
@endsection
