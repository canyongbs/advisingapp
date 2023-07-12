@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.kbItemCategory.title_singular') }}:
                        {{ trans('cruds.kbItemCategory.fields.id') }}
                        {{ $kbItemCategory->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.kbItemCategory.fields.id') }}
                                </th>
                                <td>
                                    {{ $kbItemCategory->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.kbItemCategory.fields.category') }}
                                </th>
                                <td>
                                    {{ $kbItemCategory->category }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('kb_item_category_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.kb-item-categories.edit', $kbItemCategory) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.kb-item-categories.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
