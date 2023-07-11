<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Models\JourneyTargetList;
use App\Http\Controllers\Controller;

class JourneyTargetListController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('journey_target_list_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-target-list.index');
    }

    public function create()
    {
        abort_if(Gate::denies('journey_target_list_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-target-list.create');
    }

    public function edit(JourneyTargetList $journeyTargetList)
    {
        abort_if(Gate::denies('journey_target_list_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-target-list.edit', compact('journeyTargetList'));
    }

    public function show(JourneyTargetList $journeyTargetList)
    {
        abort_if(Gate::denies('journey_target_list_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.journey-target-list.show', compact('journeyTargetList'));
    }
}
