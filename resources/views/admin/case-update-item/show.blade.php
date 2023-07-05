@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.caseUpdateItem.title_singular') }}:
                    {{ trans('cruds.caseUpdateItem.fields.id') }}
                    {{ $caseUpdateItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.caseUpdateItem.fields.id') }}
                            </th>
                            <td>
                                {{ $caseUpdateItem->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.caseUpdateItem.fields.student') }}
                            </th>
                            <td>
                                @if($caseUpdateItem->student)
                                    <span class="badge badge-relationship">{{ $caseUpdateItem->student->full ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.caseUpdateItem.fields.case') }}
                            </th>
                            <td>
                                @if($caseUpdateItem->case)
                                    <span class="badge badge-relationship">{{ $caseUpdateItem->case->casenumber ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.caseUpdateItem.fields.update') }}
                            </th>
                            <td>
                                {{ $caseUpdateItem->update }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.caseUpdateItem.fields.internal') }}
                            </th>
                            <td>
                                {{ $caseUpdateItem->internal_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.caseUpdateItem.fields.direction') }}
                            </th>
                            <td>
                                {{ $caseUpdateItem->direction_label }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('case_update_item_edit')
                    <a href="{{ route('admin.case-update-items.edit', $caseUpdateItem) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.case-update-items.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection