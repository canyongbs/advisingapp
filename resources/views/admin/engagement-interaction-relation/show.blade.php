@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.engagementInteractionRelation.title_singular') }}:
                        {{ trans('cruds.engagementInteractionRelation.fields.id') }}
                        {{ $engagementInteractionRelation->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionRelation.fields.id') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionRelation->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionRelation.fields.relation') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionRelation->relation }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('engagement_interaction_relation_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.engagement-interaction-relations.edit', $engagementInteractionRelation) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.engagement-interaction-relations.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
