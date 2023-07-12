@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.engagementEmailItem.title_singular') }}:
                        {{ trans('cruds.engagementEmailItem.fields.id') }}
                        {{ $engagementEmailItem->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementEmailItem.fields.id') }}
                                </th>
                                <td>
                                    {{ $engagementEmailItem->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementEmailItem.fields.email') }}
                                </th>
                                <td>
                                    <a
                                        class="link-light-blue"
                                        href="mailto:{{ $engagementEmailItem->email }}"
                                    >
                                        <i class="far fa-envelope fa-fw">
                                        </i>
                                        {{ $engagementEmailItem->email }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementEmailItem.fields.subject') }}
                                </th>
                                <td>
                                    {{ $engagementEmailItem->subject }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementEmailItem.fields.body') }}
                                </th>
                                <td>
                                    {{ $engagementEmailItem->body }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('engagement_email_item_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.engagement-email-items.edit', $engagementEmailItem) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.engagement-email-items.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
