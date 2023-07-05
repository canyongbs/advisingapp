<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JourneyTargetList;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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
