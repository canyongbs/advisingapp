@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.journeyTargetList.title_singular') }}:
                    {{ trans('cruds.journeyTargetList.fields.id') }}
                    {{ $journeyTargetList->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTargetList.fields.id') }}
                            </th>
                            <td>
                                {{ $journeyTargetList->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTargetList.fields.name') }}
                            </th>
                            <td>
                                {{ $journeyTargetList->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTargetList.fields.description') }}
                            </th>
                            <td>
                                {{ $journeyTargetList->description }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTargetList.fields.query') }}
                            </th>
                            <td>
                                {{ $journeyTargetList->query }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTargetList.fields.population') }}
                            </th>
                            <td>
                                {{ $journeyTargetList->population }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('journey_target_list_edit')
                    <a href="{{ route('admin.journey-target-lists.edit', $journeyTargetList) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.journey-target-lists.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection