@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.engagementInteractionRelation.title_singular') }}:
                    {{ trans('cruds.engagementInteractionRelation.fields.id') }}
                    {{ $engagementInteractionRelation->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('engagement-interaction-relation.edit', [$engagementInteractionRelation])
        </div>
    </div>
</div>
@endsection