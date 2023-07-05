@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.prospectItem.title_singular') }}:
                    {{ trans('cruds.prospectItem.fields.id') }}
                    {{ $prospectItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('prospect-item.edit', [$prospectItem])
        </div>
    </div>
</div>
@endsection