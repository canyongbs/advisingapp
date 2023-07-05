@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.prospectStatus.title_singular') }}:
                    {{ trans('cruds.prospectStatus.fields.id') }}
                    {{ $prospectStatus->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.prospectStatus.fields.id') }}
                            </th>
                            <td>
                                {{ $prospectStatus->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectStatus.fields.status') }}
                            </th>
                            <td>
                                {{ $prospectStatus->status }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('prospect_status_edit')
                    <a href="{{ route('admin.prospect-statuses.edit', $prospectStatus) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.prospect-statuses.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection