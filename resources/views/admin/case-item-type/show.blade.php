@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.caseItemType.title_singular') }}:
                    {{ trans('cruds.caseItemType.fields.id') }}
                    {{ $caseItemType->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.caseItemType.fields.id') }}
                            </th>
                            <td>
                                {{ $caseItemType->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.caseItemType.fields.type') }}
                            </th>
                            <td>
                                {{ $caseItemType->type }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('case_item_type_edit')
                    <a href="{{ route('admin.case-item-types.edit', $caseItemType) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.case-item-types.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection