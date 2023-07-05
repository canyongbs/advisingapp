@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.supportPage.title_singular') }}:
                    {{ trans('cruds.supportPage.fields.id') }}
                    {{ $supportPage->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('support-page.edit', [$supportPage])
        </div>
    </div>
</div>
@endsection