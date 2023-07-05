@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.role.title_singular') }}:
                    {{ trans('cruds.role.fields.id') }}
                    {{ $role->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('role.edit', [$role])
        </div>
    </div>
</div>
@endsection