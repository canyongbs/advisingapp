@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.journeyTargetList.title_singular') }}:
                    {{ trans('cruds.journeyTargetList.fields.id') }}
                    {{ $journeyTargetList->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('journey-target-list.edit', [$journeyTargetList])
        </div>
    </div>
</div>
@endsection