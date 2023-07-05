@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.auditLog.title_singular') }}:
                    {{ trans('cruds.auditLog.fields.id') }}
                    {{ $auditLog->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.auditLog.fields.id') }}
                            </th>
                            <td>
                                {{ $auditLog->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.auditLog.fields.description') }}
                            </th>
                            <td>
                                {{ $auditLog->description }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.auditLog.fields.subject_id') }}
                            </th>
                            <td>
                                {{ $auditLog->subject_id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.auditLog.fields.subject_type') }}
                            </th>
                            <td>
                                {{ $auditLog->subject_type }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.auditLog.fields.user_id') }}
                            </th>
                            <td>
                                {{ $auditLog->user_id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.auditLog.fields.properties') }}
                            </th>
                            <td>
                                {{ $auditLog->properties }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.auditLog.fields.host') }}
                            </th>
                            <td>
                                {{ $auditLog->host }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.auditLog.fields.created_at') }}
                            </th>
                            <td>
                                {{ $auditLog->created_at }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.auditLog.fields.updated_at') }}
                            </th>
                            <td>
                                {{ $auditLog->updated_at }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('audit_log_edit')
                    <a href="{{ route('admin.audit-logs.edit', $auditLog) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection