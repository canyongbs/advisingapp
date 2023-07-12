@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-white">
            <div class="card-header border-b border-blueGray-200">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('cruds.engagementStudentFile.title_singular') }}
                        {{ trans('global.list') }}
                    </h6>

                    @can('engagement_student_file_create')
                        <a
                            class="btn btn-indigo"
                            href="{{ route('admin.engagement-student-files.create') }}"
                        >
                            {{ trans('global.add') }} {{ trans('cruds.engagementStudentFile.title_singular') }}
                        </a>
                    @endcan
                </div>
            </div>
            @livewire('engagement-student-file.index')

        </div>
    </div>
@endsection
