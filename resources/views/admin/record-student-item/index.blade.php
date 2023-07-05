@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-white">
        <div class="card-header border-b border-blueGray-200">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('cruds.recordStudentItem.title_singular') }}
                    {{ trans('global.list') }}
                </h6>

                @can('record_student_item_create')
                    <a class="btn btn-indigo" href="{{ route('admin.record-student-items.create') }}">
                        {{ trans('global.add') }} {{ trans('cruds.recordStudentItem.title_singular') }}
                    </a>
                @endcan
            </div>
        </div>
        @livewire('record-student-item.index')

    </div>
</div>
@endsection