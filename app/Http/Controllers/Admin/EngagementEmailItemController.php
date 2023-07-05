<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EngagementEmailItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EngagementEmailItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('engagement_email_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-email-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('engagement_email_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-email-item.create');
    }

    public function show(EngagementEmailItem $engagementEmailItem)
    {
        abort_if(Gate::denies('engagement_email_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-email-item.show', compact('engagementEmailItem'));
    }
}
