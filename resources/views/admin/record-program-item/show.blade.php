@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.recordProgramItem.title_singular') }}:
                    {{ trans('cruds.recordProgramItem.fields.id') }}
                    {{ $recordProgramItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.recordProgramItem.fields.id') }}
                            </th>
                            <td>
                                {{ $recordProgramItem->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordProgramItem.fields.name') }}
                            </th>
                            <td>
                                {{ $recordProgramItem->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordProgramItem.fields.institution') }}
                            </th>
                            <td>
                                {{ $recordProgramItem->institution }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordProgramItem.fields.plan') }}
                            </th>
                            <td>
                                {{ $recordProgramItem->plan }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordProgramItem.fields.career') }}
                            </th>
                            <td>
                                {{ $recordProgramItem->career }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordProgramItem.fields.term') }}
                            </th>
                            <td>
                                {{ $recordProgramItem->term }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordProgramItem.fields.status') }}
                            </th>
                            <td>
                                {{ $recordProgramItem->status }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordProgramItem.fields.foi') }}
                            </th>
                            <td>
                                {{ $recordProgramItem->foi }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordProgramItem.fields.gpa') }}
                            </th>
                            <td>
                                {{ $recordProgramItem->gpa }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('record_program_item_edit')
                    <a href="{{ route('admin.record-program-items.edit', $recordProgramItem) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.record-program-items.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection