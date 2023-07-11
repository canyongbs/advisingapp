<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\JourneyItem;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class JourneyItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('journey_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('journey_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-item.create');
    }

    public function edit(JourneyItem $journeyItem)
    {
        abort_if(Gate::denies('journey_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-item.edit', compact('journeyItem'));
    }

    public function show(JourneyItem $journeyItem)
    {
        abort_if(Gate::denies('journey_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-item.show', compact('journeyItem'));
    }
}
