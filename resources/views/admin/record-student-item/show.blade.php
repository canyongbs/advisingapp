@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.recordStudentItem.title_singular') }}:
                    {{ trans('cruds.recordStudentItem.fields.id') }}
                    {{ $recordStudentItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.id') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.sisid') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->sisid }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.otherid') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->otherid }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.first') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->first }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.last') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->last }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.full') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->full }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.preferred') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->preferred }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.email') }}
                            </th>
                            <td>
                                <a class="link-light-blue" href="mailto:{{ $recordStudentItem->email }}">
                                    <i class="far fa-envelope fa-fw">
                                    </i>
                                    {{ $recordStudentItem->email }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.email_2') }}
                            </th>
                            <td>
                                <a class="link-light-blue" href="mailto:{{ $recordStudentItem->email_2 }}">
                                    <i class="far fa-envelope fa-fw">
                                    </i>
                                    {{ $recordStudentItem->email_2 }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.mobile') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->mobile }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.sms_opt_out') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->sms_opt_out_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.email_bounce') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->email_bounce_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.phone') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->phone }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.address') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->address }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.address_2') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->address_2 }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.birthdate') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->birthdate }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.hsgrad') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->hsgrad }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.dual') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->dual_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.ferpa') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->ferpa_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.gpa') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->gpa }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.dfw') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->dfw }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.firstgen') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->firstgen_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.ethnicity') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->ethnicity }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.recordStudentItem.fields.lastlmslogin') }}
                            </th>
                            <td>
                                {{ $recordStudentItem->lastlmslogin }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('record_student_item_edit')
                    <a href="{{ route('admin.record-student-items.edit', $recordStudentItem) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.record-student-items.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection