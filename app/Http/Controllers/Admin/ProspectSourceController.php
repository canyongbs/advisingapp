<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Models\ProspectSource;
use App\Http\Controllers\Controller;

class ProspectSourceController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('prospect_source_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-source.index');
    }

    public function create()
    {
        abort_if(Gate::denies('prospect_source_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-source.create');
    }

    public function edit(ProspectSource $prospectSource)
    {
        abort_if(Gate::denies('prospect_source_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-source.edit', compact('prospectSource'));
    }

    public function show(ProspectSource $prospectSource)
    {
        abort_if(Gate::denies('prospect_source_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-source.show', compact('prospectSource'));
    }
}
