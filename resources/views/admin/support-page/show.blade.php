@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.supportPage.title_singular') }}:
                    {{ trans('cruds.supportPage.fields.id') }}
                    {{ $supportPage->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.supportPage.fields.id') }}
                            </th>
                            <td>
                                {{ $supportPage->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.supportPage.fields.title') }}
                            </th>
                            <td>
                                {{ $supportPage->title }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.supportPage.fields.body') }}
                            </th>
                            <td>
                                {{ $supportPage->body }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('support_page_edit')
                    <a href="{{ route('admin.support-pages.edit', $supportPage) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.support-pages.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection