@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.user.title_singular') }}:
                        {{ trans('cruds.user.fields.id') }}
                        {{ $user->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.id') }}
                                </th>
                                <td>
                                    {{ $user->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.emplid') }}
                                </th>
                                <td>
                                    {{ $user->emplid }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.name') }}
                                </th>
                                <td>
                                    {{ $user->name }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.email') }}
                                </th>
                                <td>
                                    <a
                                        class="link-light-blue"
                                        href="mailto:{{ $user->email }}"
                                    >
                                        <i class="far fa-envelope fa-fw">
                                        </i>
                                        {{ $user->email }}
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.email_verified_at') }}
                                </th>
                                <td>
                                    {{ $user->email_verified_at }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.roles') }}
                                </th>
                                <td>
                                    @foreach ($user->roles as $key => $entry)
                                        <span class="badge badge-relationship">{{ $entry->title }}</span>
                                    @endforeach
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.locale') }}
                                </th>
                                <td>
                                    {{ $user->locale }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.user.fields.type') }}
                                </th>
                                <td>
                                    {{ $user->type_label }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('user_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.users.edit', $user) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.users.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
