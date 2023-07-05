@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.institution.title_singular') }}:
                    {{ trans('cruds.institution.fields.id') }}
                    {{ $institution->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.institution.fields.id') }}
                            </th>
                            <td>
                                {{ $institution->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.institution.fields.code') }}
                            </th>
                            <td>
                                {{ $institution->code }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.institution.fields.name') }}
                            </th>
                            <td>
                                {{ $institution->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.institution.fields.description') }}
                            </th>
                            <td>
                                {{ $institution->description }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('institution_edit')
                    <a href="{{ route('admin.institutions.edit', $institution) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.institutions.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection