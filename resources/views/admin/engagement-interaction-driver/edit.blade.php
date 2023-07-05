@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.engagementInteractionDriver.title_singular') }}:
                    {{ trans('cruds.engagementInteractionDriver.fields.id') }}
                    {{ $engagementInteractionDriver->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('engagement-interaction-driver.edit', [$engagementInteractionDriver])
        </div>
    </div>
</div>
@endsection