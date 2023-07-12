@extends('layouts.admin')
@section('content')
    <div class="row">
        <div class="card bg-blueGray-100">
            <div class="card-header">
                <div class="card-header-container">
                    <h6 class="card-title">
                        {{ trans('global.view') }}
                        {{ trans('cruds.engagementInteractionOutcome.title_singular') }}:
                        {{ trans('cruds.engagementInteractionOutcome.fields.id') }}
                        {{ $engagementInteractionOutcome->id }}
                    </h6>
                </div>
            </div>

            <div class="card-body">
                <div class="pt-3">
                    <table class="table-view table">
                        <tbody class="bg-white">
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionOutcome.fields.id') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionOutcome->id }}
                                </td>
                            </tr>
                            <tr>
                                <th>
                                    {{ trans('cruds.engagementInteractionOutcome.fields.outcome') }}
                                </th>
                                <td>
                                    {{ $engagementInteractionOutcome->outcome }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    @can('engagement_interaction_outcome_edit')
                        <a
                            class="btn btn-indigo mr-2"
                            href="{{ route('admin.engagement-interaction-outcomes.edit', $engagementInteractionOutcome) }}"
                        >
                            {{ trans('global.edit') }}
                        </a>
                    @endcan
                    <a
                        class="btn btn-secondary"
                        href="{{ route('admin.engagement-interaction-outcomes.index') }}"
                    >
                        {{ trans('global.back') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
