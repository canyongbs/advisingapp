@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.prospectSource.title_singular') }}:
                        {{ trans('cruds.prospectSource.fields.id') }}
                        {{ $prospectSource->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.prospectSource.fields.id') }}
                                </th>
                                <td>
                                    {{ $prospectSource->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.prospectSource.fields.source') }}
                                </th>
                                <td>
                                    {{ $prospectSource->source }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('prospect_source_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.prospect-sources.edit', $prospectSource) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.prospect-sources.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
