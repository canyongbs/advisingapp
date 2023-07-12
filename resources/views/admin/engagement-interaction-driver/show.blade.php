@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.engagementInteractionDriver.title_singular') }}:
                        {{ trans('cruds.engagementInteractionDriver.fields.id') }}
                        {{ $engagementInteractionDriver->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionDriver.fields.id') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionDriver->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionDriver.fields.driver') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionDriver->driver }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('engagement_interaction_driver_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.engagement-interaction-drivers.edit', $engagementInteractionDriver) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.engagement-interaction-drivers.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
