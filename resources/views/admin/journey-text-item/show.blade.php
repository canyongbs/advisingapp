@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.journeyTextItem.title_singular') }}:
                    {{ trans('cruds.journeyTextItem.fields.id') }}
                    {{ $journeyTextItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTextItem.fields.id') }}
                            </th>
                            <td>
                                {{ $journeyTextItem->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTextItem.fields.name') }}
                            </th>
                            <td>
                                {{ $journeyTextItem->name }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTextItem.fields.text') }}
                            </th>
                            <td>
                                {{ $journeyTextItem->text }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTextItem.fields.start') }}
                            </th>
                            <td>
                                {{ $journeyTextItem->start }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTextItem.fields.end') }}
                            </th>
                            <td>
                                {{ $journeyTextItem->end }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTextItem.fields.active') }}
                            </th>
                            <td>
                                {{ $journeyTextItem->active_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.journeyTextItem.fields.frequency') }}
                            </th>
                            <td>
                                {{ $journeyTextItem->frequency_label }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('journey_text_item_edit')
                    <a href="{{ route('admin.journey-text-items.edit', $journeyTextItem) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.journey-text-items.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection