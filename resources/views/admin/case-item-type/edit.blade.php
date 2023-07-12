@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.edit') }}
                        {{ trans('cruds.caseItemType.title_singular') }}:
                        {{ trans('cruds.caseItemType.fields.id') }}
                        {{ $caseItemType->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                @livewire('case-item-type.edit', [$caseItemType])
            </div>
        </div>
    </div>
@endsection
