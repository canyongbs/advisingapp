<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SupportItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('support_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('support_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-item.create');
    }

    public function edit(SupportItem $supportItem)
    {
        abort_if(Gate::denies('support_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-item.edit', compact('supportItem'));
    }

    public function show(SupportItem $supportItem)
    {
        abort_if(Gate::denies('support_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-item.show', compact('supportItem'));
    }
}
