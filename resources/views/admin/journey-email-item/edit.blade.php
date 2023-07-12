@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.edit') }}
                        {{ trans('cruds.journeyEmailItem.title_singular') }}:
                        {{ trans('cruds.journeyEmailItem.fields.id') }}
                        {{ $journeyEmailItem->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                @livewire('journey-email-item.edit', [$journeyEmailItem])
            </div>
        </div>
    </div>
@endsection
