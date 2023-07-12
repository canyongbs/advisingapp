@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.edit') }}
                        {{ trans('cruds.caseItemPriority.title_singular') }}:
                        {{ trans('cruds.caseItemPriority.fields.id') }}
                        {{ $caseItemPriority->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                @livewire('case-item-priority.edit', [$caseItemPriority])
            </div>
        </div>
    </div>
@endsection
