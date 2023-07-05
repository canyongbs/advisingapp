@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.caseItemPriority.title_singular') }}:
                    {{ trans('cruds.caseItemPriority.fields.id') }}
                    {{ $caseItemPriority->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.caseItemPriority.fields.id') }}
                            </th>
                            <td>
                                {{ $caseItemPriority->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.caseItemPriority.fields.priority') }}
                            </th>
                            <td>
                                {{ $caseItemPriority->priority }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('case_item_priority_edit')
                    <a href="{{ route('admin.case-item-priorities.edit', $caseItemPriority) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.case-item-priorities.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection