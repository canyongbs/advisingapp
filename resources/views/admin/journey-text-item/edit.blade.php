@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.edit') }}
                        {{ trans('cruds.journeyTextItem.title_singular') }}:
                        {{ trans('cruds.journeyTextItem.fields.id') }}
                        {{ $journeyTextItem->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                @livewire('journey-text-item.edit', [$journeyTextItem])
            </div>
        </div>
    </div>
@endsection
