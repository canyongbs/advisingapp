<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportProspect;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportProspectController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('report_prospect_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.report-prospect.index');
    }

    public function create()
    {
        abort_if(Gate::denies('report_prospect_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.report-prospect.create');
    }

    public function edit(ReportProspect $reportProspect)
    {
        abort_if(Gate::denies('report_prospect_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.report-prospect.edit', compact('reportProspect'));
    }

    public function show(ReportProspect $reportProspect)
    {
        abort_if(Gate::denies('report_prospect_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.report-prospect.show', compact('reportProspect'));
    }
}
