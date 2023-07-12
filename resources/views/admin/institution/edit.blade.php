@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.edit') }}
                        {{ trans('cruds.institution.title_singular') }}:
                        {{ trans('cruds.institution.fields.id') }}
                        {{ $institution->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                @livewire('institution.edit', [$institution])
            </div>
        </div>
    </div>
@endsection
