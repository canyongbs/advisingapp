@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.edit') }}
                        {{ trans('cruds.kbItemQuality.title_singular') }}:
                        {{ trans('cruds.kbItemQuality.fields.id') }}
                        {{ $kbItemQuality->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                @livewire('kb-item-quality.edit', [$kbItemQuality])
            </div>
        </div>
    </div>
@endsection
