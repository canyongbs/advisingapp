@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.permission.title_singular') }}:
                    {{ trans('cruds.permission.fields.id') }}
                    {{ $permission->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('permission.edit', [$permission])
        </div>
    </div>
</div>
@endsection