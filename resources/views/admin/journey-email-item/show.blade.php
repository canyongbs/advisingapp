@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.journeyEmailItem.title_singular') }}:
                        {{ trans('cruds.journeyEmailItem.fields.id') }}
                        {{ $journeyEmailItem->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyEmailItem.fields.id') }}
                                </th>
                                <td>
                                    {{ $journeyEmailItem->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyEmailItem.fields.name') }}
                                </th>
                                <td>
                                    {{ $journeyEmailItem->name }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyEmailItem.fields.body') }}
                                </th>
                                <td>
                                    {{ $journeyEmailItem->body }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyEmailItem.fields.start') }}
                                </th>
                                <td>
                                    {{ $journeyEmailItem->start }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyEmailItem.fields.end') }}
                                </th>
                                <td>
                                    {{ $journeyEmailItem->end }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyEmailItem.fields.active') }}
                                </th>
                                <td>
                                    {{ $journeyEmailItem->active_label }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.journeyEmailItem.fields.frequency') }}
                                </th>
                                <td>
                                    {{ $journeyEmailItem->frequency_label }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('journey_email_item_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.journey-email-items.edit', $journeyEmailItem) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.journey-email-items.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
