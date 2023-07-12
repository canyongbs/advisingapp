@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.caseItem.title_singular') }}:
                        {{ trans('cruds.caseItem.fields.id') }}
                        {{ $caseItem->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.id') }}
                                </th>
                                <td>
                                    {{ $caseItem->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.casenumber') }}
                                </th>
                                <td>
                                    {{ $caseItem->casenumber }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.student') }}
                                </th>
                                <td>
                                    @if ($caseItem->student)
                                        <span class="badge badge-relationship">{{ $caseItem->student->full ?? '' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.institution') }}
                                </th>
                                <td>
                                    @if ($caseItem->institution)
                                        <span
                                            class="badge badge-relationship">{{ $caseItem->institution->name ?? '' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.state') }}
                                </th>
                                <td>
                                    @if ($caseItem->state)
                                        <span class="badge badge-relationship">{{ $caseItem->state->status ?? '' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.type') }}
                                </th>
                                <td>
                                    @if ($caseItem->type)
                                        <span class="badge badge-relationship">{{ $caseItem->type->type ?? '' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.priority') }}
                                </th>
                                <td>
                                    @if ($caseItem->priority)
                                        <span
                                            class="badge badge-relationship">{{ $caseItem->priority->priority ?? '' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.assigned_to') }}
                                </th>
                                <td>
                                    @if ($caseItem->assignedTo)
                                        <span
                                            class="badge badge-relationship">{{ $caseItem->assignedTo->name ?? '' }}</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.close_details') }}
                                </th>
                                <td>
                                    {{ $caseItem->close_details }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.res_details') }}
                                </th>
                                <td>
                                    {{ $caseItem->res_details }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.caseItem.fields.created_by') }}
                                </th>
                                <td>
                                    @if ($caseItem->createdBy)
                                        <span
                                            class="badge badge-relationship">{{ $caseItem->createdBy->name ?? '' }}</span>
                                    @endif
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('case_item_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.case-items.edit', $caseItem) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.case-items.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
