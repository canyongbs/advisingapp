@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.kbItemStatus.title_singular') }}:
                        {{ trans('cruds.kbItemStatus.fields.id') }}
                        {{ $kbItemStatus->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.kbItemStatus.fields.id') }}
                                </th>
                                <td>
                                    {{ $kbItemStatus->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.kbItemStatus.fields.status') }}
                                </th>
                                <td>
                                    {{ $kbItemStatus->status }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('kb_item_status_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.kb-item-statuses.edit', $kbItemStatus) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.kb-item-statuses.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
