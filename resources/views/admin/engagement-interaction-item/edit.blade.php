@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.engagementInteractionItem.title_singular') }}:
                    {{ trans('cruds.engagementInteractionItem.fields.id') }}
                    {{ $engagementInteractionItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('engagement-interaction-item.edit', [$engagementInteractionItem])
        </div>
    </div>
</div>
@endsection