@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.kbItem.title_singular') }}:
                    {{ trans('cruds.kbItem.fields.id') }}
                    {{ $kbItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.kbItem.fields.id') }}
                            </th>
                            <td>
                                {{ $kbItem->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.kbItem.fields.question') }}
                            </th>
                            <td>
                                {{ $kbItem->question }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.kbItem.fields.quality') }}
                            </th>
                            <td>
                                @if($kbItem->quality)
                                    <span class="badge badge-relationship">{{ $kbItem->quality->rating ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.kbItem.fields.status') }}
                            </th>
                            <td>
                                @if($kbItem->status)
                                    <span class="badge badge-relationship">{{ $kbItem->status->status ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.kbItem.fields.public') }}
                            </th>
                            <td>
                                {{ $kbItem->public_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.kbItem.fields.category') }}
                            </th>
                            <td>
                                @if($kbItem->category)
                                    <span class="badge badge-relationship">{{ $kbItem->category->category ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.kbItem.fields.institution') }}
                            </th>
                            <td>
                                @foreach($kbItem->institution as $key => $entry)
                                    <span class="badge badge-relationship">{{ $entry->name }}</span>
                                @endforeach
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.kbItem.fields.solution') }}
                            </th>
                            <td>
                                {{ $kbItem->solution }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.kbItem.fields.notes') }}
                            </th>
                            <td>
                                {{ $kbItem->notes }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('kb_item_edit')
                    <a href="{{ route('admin.kb-items.edit', $kbItem) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.kb-items.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection