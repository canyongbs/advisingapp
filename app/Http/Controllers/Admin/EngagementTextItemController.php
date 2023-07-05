<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EngagementTextItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
