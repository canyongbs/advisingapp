@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.engagementStudentFile.title_singular') }}:
                    {{ trans('cruds.engagementStudentFile.fields.id') }}
                    {{ $engagementStudentFile->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.engagementStudentFile.fields.id') }}
                            </th>
                            <td>
                                {{ $engagementStudentFile->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.engagementStudentFile.fields.file') }}
                            </th>
                            <td>
                                @foreach($engagementStudentFile->file as $key => $entry)
                                    <a class="link-light-blue" href="{{ $entry['url'] }}">
                                        <i class="far fa-file">
                                        </i>
                                        {{ $entry['file_name'] }}
                                    </a>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.engagementStudentFile.fields.description') }}
                            </th>
                            <td>
                                {{ $engagementStudentFile->description }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.engagementStudentFile.fields.student') }}
                            </th>
                            <td>
                                @if($engagementStudentFile->student)
                                    <span class="badge badge-relationship">{{ $engagementStudentFile->student->full ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('engagement_student_file_edit')
                    <a href="{{ route('admin.engagement-student-files.edit', $engagementStudentFile) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.engagement-student-files.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection