@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.kbItem.title_singular') }}:
                    {{ trans('cruds.kbItem.fields.id') }}
                    {{ $kbItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('kb-item.edit', [$kbItem])
        </div>
    </div>
</div>
@endsection