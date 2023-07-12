@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.caseItemStatus.title_singular') }}:
                        {{ trans('cruds.caseItemStatus.fields.id') }}
                        {{ $caseItemStatus->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItemStatus.fields.id') }}
                                </th>
                                <td>
                                    {{ $caseItemStatus->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItemStatus.fields.status') }}
                                </th>
                                <td>
                                    {{ $caseItemStatus->status }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('case_item_status_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.case-item-statuses.edit', $caseItemStatus) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.case-item-statuses.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
