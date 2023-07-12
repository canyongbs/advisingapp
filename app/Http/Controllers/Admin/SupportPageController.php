<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\SupportPage;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class SupportPageController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('support_page_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-page.index');
    }

    public function create()
    {
        abort_if(Gate::denies('support_page_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-page.create');
    }

    public function edit(SupportPage $supportPage)
    {
        abort_if(Gate::denies('support_page_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-page.edit', compact('supportPage'));
    }

    public function show(SupportPage $supportPage)
    {
        abort_if(Gate::denies('support_page_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-page.show', compact('supportPage'));
    }
}
