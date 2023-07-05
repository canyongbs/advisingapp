@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.userAlert.title_singular') }}:
                    {{ trans('cruds.userAlert.fields.id') }}
                    {{ $userAlert->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.userAlert.fields.id') }}
                            </th>
                            <td>
                                {{ $userAlert->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.userAlert.fields.message') }}
                            </th>
                            <td>
                                {{ $userAlert->message }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.userAlert.fields.link') }}
                            </th>
                            <td>
                                {{ $userAlert->link }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.userAlert.fields.users') }}
                            </th>
                            <td>
                                @foreach($userAlert->users as $key => $entry)
                                    <span class="badge badge-relationship">{{ $entry->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('user_alert_edit')
                    <a href="{{ route('admin.user-alerts.edit', $userAlert) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.user-alerts.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection