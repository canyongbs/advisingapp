<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\EngagementInteractionItem;

class EngagementInteractionItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('engagement_interaction_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('engagement_interaction_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-item.create');
    }

    public function edit(EngagementInteractionItem $engagementInteractionItem)
    {
        abort_if(Gate::denies('engagement_interaction_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-item.edit', compact('engagementInteractionItem'));
    }

    public function show(EngagementInteractionItem $engagementInteractionItem)
    {
        abort_if(Gate::denies('engagement_interaction_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-item.show', compact('engagementInteractionItem'));
    }
}
