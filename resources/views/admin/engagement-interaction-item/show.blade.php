@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.engagementInteractionItem.title_singular') }}:
                        {{ trans('cruds.engagementInteractionItem.fields.id') }}
                        {{ $engagementInteractionItem->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionItem.fields.id') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionItem->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionItem.fields.direction') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionItem->direction_label }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionItem.fields.start') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionItem->start }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionItem.fields.duration') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionItem->duration_label }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionItem.fields.subject') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionItem->subject }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionItem.fields.description') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionItem->description }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionItem.fields.created_at') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionItem->created_at }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('engagement_interaction_item_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.engagement-interaction-items.edit', $engagementInteractionItem) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.engagement-interaction-items.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
