<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProspectStatus;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProspectStatusController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('prospect_status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-status.index');
    }

    public function create()
    {
        abort_if(Gate::denies('prospect_status_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-status.create');
    }

    public function edit(ProspectStatus $prospectStatus)
    {
        abort_if(Gate::denies('prospect_status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-status.edit', compact('prospectStatus'));
    }

    public function show(ProspectStatus $prospectStatus)
    {
        abort_if(Gate::denies('prospect_status_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-status.show', compact('prospectStatus'));
    }
}
