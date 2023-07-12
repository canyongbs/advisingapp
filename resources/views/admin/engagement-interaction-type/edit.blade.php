@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.edit') }}
                        {{ trans('cruds.engagementInteractionType.title_singular') }}:
                        {{ trans('cruds.engagementInteractionType.fields.id') }}
                        {{ $engagementInteractionType->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                @livewire('engagement-interaction-type.edit', [$engagementInteractionType])
            </div>
        </div>
    </div>
@endsection
