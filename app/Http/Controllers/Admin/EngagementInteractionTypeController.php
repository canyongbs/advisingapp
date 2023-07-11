<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\EngagementInteractionType;

class EngagementInteractionTypeController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('engagement_interaction_type_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-type.index');
    }

    public function create()
    {
        abort_if(Gate::denies('engagement_interaction_type_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-type.create');
    }

    public function edit(EngagementInteractionType $engagementInteractionType)
    {
        abort_if(Gate::denies('engagement_interaction_type_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-type.edit', compact('engagementInteractionType'));
    }

    public function show(EngagementInteractionType $engagementInteractionType)
    {
        abort_if(Gate::denies('engagement_interaction_type_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-type.show', compact('engagementInteractionType'));
    }
}
