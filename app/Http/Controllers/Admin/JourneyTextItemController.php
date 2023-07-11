<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Models\JourneyTextItem;
use App\Http\Controllers\Controller;

class JourneyTextItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('journey_text_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-text-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('journey_text_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-text-item.create');
    }

    public function edit(JourneyTextItem $journeyTextItem)
    {
        abort_if(Gate::denies('journey_text_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-text-item.edit', compact('journeyTextItem'));
    }

    public function show(JourneyTextItem $journeyTextItem)
    {
        abort_if(Gate::denies('journey_text_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-text-item.show', compact('journeyTextItem'));
    }
}
