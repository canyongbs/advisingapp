@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.recordEnrollmentItem.title_singular') }}:
                    {{ trans('cruds.recordEnrollmentItem.fields.id') }}
                    {{ $recordEnrollmentItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.recordEnrollmentItem.fields.id') }}
                            </th>
                            <td>
                                {{ $recordEnrollmentItem->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordEnrollmentItem.fields.sisid') }}
                            </th>
                            <td>
                                {{ $recordEnrollmentItem->sisid }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordEnrollmentItem.fields.name') }}
                            </th>
                            <td>
                                {{ $recordEnrollmentItem->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordEnrollmentItem.fields.start') }}
                            </th>
                            <td>
                                {{ $recordEnrollmentItem->start }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordEnrollmentItem.fields.end') }}
                            </th>
                            <td>
                                {{ $recordEnrollmentItem->end }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordEnrollmentItem.fields.course') }}
                            </th>
                            <td>
                                {{ $recordEnrollmentItem->course }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordEnrollmentItem.fields.grade') }}
                            </th>
                            <td>
                                {{ $recordEnrollmentItem->grade }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('record_enrollment_item_edit')
                    <a href="{{ route('admin.record-enrollment-items.edit', $recordEnrollmentItem) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.record-enrollment-items.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection