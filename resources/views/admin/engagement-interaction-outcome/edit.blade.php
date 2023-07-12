@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.edit') }}
                        {{ trans('cruds.engagementInteractionOutcome.title_singular') }}:
                        {{ trans('cruds.engagementInteractionOutcome.fields.id') }}
                        {{ $engagementInteractionOutcome->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                @livewire('engagement-interaction-outcome.edit', [$engagementInteractionOutcome])
            </div>
        </div>
    </div>
@endsection
