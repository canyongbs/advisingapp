@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.engagementTextItem.title_singular') }}:
                        {{ trans('cruds.engagementTextItem.fields.id') }}
                        {{ $engagementTextItem->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementTextItem.fields.id') }}
                                </th>
                                <td>
                                    {{ $engagementTextItem->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementTextItem.fields.direction') }}
                                </th>
                                <td>
                                    {{ $engagementTextItem->direction_label }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementTextItem.fields.mobile') }}
                                </th>
                                <td>
                                    {{ $engagementTextItem->mobile }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementTextItem.fields.message') }}
                                </th>
                                <td>
                                    {{ $engagementTextItem->message }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('engagement_text_item_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.engagement-text-items.edit', $engagementTextItem) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.engagement-text-items.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
