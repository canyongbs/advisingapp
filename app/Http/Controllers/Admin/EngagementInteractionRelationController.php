<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EngagementInteractionRelation;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EngagementInteractionRelationController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('engagement_interaction_relation_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-relation.index');
    }

    public function create()
    {
        abort_if(Gate::denies('engagement_interaction_relation_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-relation.create');
    }

    public function edit(EngagementInteractionRelation $engagementInteractionRelation)
    {
        abort_if(Gate::denies('engagement_interaction_relation_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-relation.edit', compact('engagementInteractionRelation'));
    }

    public function show(EngagementInteractionRelation $engagementInteractionRelation)
    {
        abort_if(Gate::denies('engagement_interaction_relation_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-relation.show', compact('engagementInteractionRelation'));
    }
}
