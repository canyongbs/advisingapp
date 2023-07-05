@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.kbItemQuality.title_singular') }}:
                    {{ trans('cruds.kbItemQuality.fields.id') }}
                    {{ $kbItemQuality->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.kbItemQuality.fields.id') }}
                            </th>
                            <td>
                                {{ $kbItemQuality->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.kbItemQuality.fields.rating') }}
                            </th>
                            <td>
                                {{ $kbItemQuality->rating }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('kb_item_quality_edit')
                    <a href="{{ route('admin.kb-item-qualities.edit', $kbItemQuality) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.kb-item-qualities.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection