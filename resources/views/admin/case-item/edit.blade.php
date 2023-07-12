@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.edit') }}
                        {{ trans('cruds.caseItem.title_singular') }}:
                        {{ trans('cruds.caseItem.fields.id') }}
                        {{ $caseItem->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                @livewire('case-item.edit', [$caseItem])
            </div>
        </div>
    </div>
@endsection
