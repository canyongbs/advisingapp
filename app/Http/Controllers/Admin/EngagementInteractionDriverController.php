<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Models\EngagementInteractionDriver;
use App\Http\Controllers\Traits\WithCSVImport;

class EngagementInteractionDriverController extends Controller
{
    use WithCSVImport;

    public function __construct()
    {
        $this->csvImportModel = EngagementInteractionDriver::class;
    }

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
}
