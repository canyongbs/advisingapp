<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JourneyEmailItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JourneyEmailItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('journey_email_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-email-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('journey_email_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-email-item.create');
    }

    public function edit(JourneyEmailItem $journeyEmailItem)
    {
        abort_if(Gate::denies('journey_email_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-email-item.edit', compact('journeyEmailItem'));
    }

    public function show(JourneyEmailItem $journeyEmailItem)
    {
        abort_if(Gate::denies('journey_email_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-email-item.show', compact('journeyEmailItem'));
    }
}
