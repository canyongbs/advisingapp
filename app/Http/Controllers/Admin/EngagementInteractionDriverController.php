<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\WithCSVImport;
use App\Models\EngagementInteractionDriver;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EngagementInteractionDriverController extends Controller
{
    use WithCSVImport;

    public function index()
    {
        abort_if(Gate::denies('engagement_interaction_driver_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-driver.index');
    }

    public function create()
    {
        abort_if(Gate::denies('engagement_interaction_driver_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-driver.create');
    }

    public function edit(EngagementInteractionDriver $engagementInteractionDriver)
    {
        abort_if(Gate::denies('engagement_interaction_driver_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-driver.edit', compact('engagementInteractionDriver'));
    }

    public function show(EngagementInteractionDriver $engagementInteractionDriver)
    {
        abort_if(Gate::denies('engagement_interaction_driver_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-driver.show', compact('engagementInteractionDriver'));
    }

    public function __construct()
    {
        $this->csvImportModel = EngagementInteractionDriver::class;
    }
}
