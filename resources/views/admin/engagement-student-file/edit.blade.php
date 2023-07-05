@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.edit') }}
                    {{ trans('cruds.engagementStudentFile.title_singular') }}:
                    {{ trans('cruds.engagementStudentFile.fields.id') }}
                    {{ $engagementStudentFile->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            @livewire('engagement-student-file.edit', [$engagementStudentFile])
        </div>
    </div>
</div>
@endsection