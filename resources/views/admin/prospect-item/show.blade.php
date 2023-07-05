@extends('layouts.admin')
@section('content')
<div class="row">
    <div class="card bg-blueGray-100">
        <div class="card-header">
            <div class="card-header-container">
                <h6 class="card-title">
                    {{ trans('global.view') }}
                    {{ trans('cruds.prospectItem.title_singular') }}:
                    {{ trans('cruds.prospectItem.fields.id') }}
                    {{ $prospectItem->id }}
                </h6>
            </div>
        </div>

        <div class="card-body">
            <div class="pt-3">
                <table class="table table-view">
                    <tbody class="bg-white">
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.id') }}
                            </th>
                            <td>
                                {{ $prospectItem->id }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.first') }}
                            </th>
                            <td>
                                {{ $prospectItem->first }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.last') }}
                            </th>
                            <td>
                                {{ $prospectItem->last }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.full') }}
                            </th>
                            <td>
                                {{ $prospectItem->full }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.preferred') }}
                            </th>
                            <td>
                                {{ $prospectItem->preferred }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.description') }}
                            </th>
                            <td>
                                {{ $prospectItem->description }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.email') }}
                            </th>
                            <td>
                                <a class="link-light-blue" href="mailto:{{ $prospectItem->email }}">
                                    <i class="far fa-envelope fa-fw">
                                    </i>
                                    {{ $prospectItem->email }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.email_2') }}
                            </th>
                            <td>
                                <a class="link-light-blue" href="mailto:{{ $prospectItem->email_2 }}">
                                    <i class="far fa-envelope fa-fw">
                                    </i>
                                    {{ $prospectItem->email_2 }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.mobile') }}
                            </th>
                            <td>
                                {{ $prospectItem->mobile }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.sms_opt_out') }}
                            </th>
                            <td>
                                {{ $prospectItem->sms_opt_out_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.email_bounce') }}
                            </th>
                            <td>
                                {{ $prospectItem->email_bounce_label }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.status') }}
                            </th>
                            <td>
                                @if($prospectItem->status)
                                    <span class="badge badge-relationship">{{ $prospectItem->status->status ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.source') }}
                            </th>
                            <td>
                                @if($prospectItem->source)
                                    <span class="badge badge-relationship">{{ $prospectItem->source->source ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.phone') }}
                            </th>
                            <td>
                                {{ $prospectItem->phone }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.address') }}
                            </th>
                            <td>
                                {{ $prospectItem->address }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.address_2') }}
                            </th>
                            <td>
                                {{ $prospectItem->address_2 }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.birthdate') }}
                            </th>
                            <td>
                                {{ $prospectItem->birthdate }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.hsgrad') }}
                            </th>
                            <td>
                                {{ $prospectItem->hsgrad }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.hsdate') }}
                            </th>
                            <td>
                                {{ $prospectItem->hsdate }}
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.assigned_to') }}
                            </th>
                            <td>
                                @if($prospectItem->assignedTo)
                                    <span class="badge badge-relationship">{{ $prospectItem->assignedTo->name ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>
                                {{ trans('cruds.prospectItem.fields.created_by') }}
                            </th>
                            <td>
                                @if($prospectItem->createdBy)
                                    <span class="badge badge-relationship">{{ $prospectItem->createdBy->name ?? '' }}</span>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="form-group">
                @can('prospect_item_edit')
                    <a href="{{ route('admin.prospect-items.edit', $prospectItem) }}" class="btn btn-indigo mr-2">
                        {{ trans('global.edit') }}
                    </a>
                @endcan
                <a href="{{ route('admin.prospect-items.index') }}" class="btn btn-secondary">
                    {{ trans('global.back') }}
                </a>
            </div>
        </div>
    </div>
</div>
@endsection