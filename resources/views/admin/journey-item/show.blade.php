@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.journeyItem.title_singular') }}:
                        {{ trans('cruds.journeyItem.fields.id') }}
                        {{ $journeyItem->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyItem.fields.id') }}
                                </th>
                                <td>
                                    {{ $journeyItem->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyItem.fields.name') }}
                                </th>
                                <td>
                                    {{ $journeyItem->name }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyItem.fields.body') }}
                                </th>
                                <td>
                                    {{ $journeyItem->body }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyItem.fields.start') }}
                                </th>
                                <td>
                                    {{ $journeyItem->start }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyItem.fields.end') }}
                                </th>
                                <td>
                                    {{ $journeyItem->end }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyItem.fields.frequency') }}
                                </th>
                                <td>
                                    {{ $journeyItem->frequency_label }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('journey_item_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.journey-items.edit', $journeyItem) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.journey-items.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
