<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\WithCSVImport;
use App\Models\EngagementInteractionOutcome;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EngagementInteractionOutcomeController extends Controller
{
    use WithCSVImport;

    public function index()
    {
        abort_if(Gate::denies('engagement_interaction_outcome_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-outcome.index');
    }

    public function create()
    {
        abort_if(Gate::denies('engagement_interaction_outcome_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-outcome.create');
    }

    public function edit(EngagementInteractionOutcome $engagementInteractionOutcome)
    {
        abort_if(Gate::denies('engagement_interaction_outcome_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-outcome.edit', compact('engagementInteractionOutcome'));
    }

    public function show(EngagementInteractionOutcome $engagementInteractionOutcome)
    {
        abort_if(Gate::denies('engagement_interaction_outcome_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-interaction-outcome.show', compact('engagementInteractionOutcome'));
    }

    public function __construct()
    {
        $this->csvImportModel = EngagementInteractionOutcome::class;
    }
}
