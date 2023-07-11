<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Models\EngagementTextItem;
use App\Http\Controllers\Controller;

class EngagementTextItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('engagement_text_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-text-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('engagement_text_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-text-item.create');
    }

    public function show(EngagementTextItem $engagementTextItem)
    {
        abort_if(Gate::denies('engagement_text_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-text-item.show', compact('engagementTextItem'));
    }
}
