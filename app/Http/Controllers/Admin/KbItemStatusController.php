<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\KbItemStatus;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class KbItemStatusController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('kb_item_status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-status.index');
    }

    public function create()
    {
        abort_if(Gate::denies('kb_item_status_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-status.create');
    }

    public function edit(KbItemStatus $kbItemStatus)
    {
        abort_if(Gate::denies('kb_item_status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-status.edit', compact('kbItemStatus'));
    }

    public function show(KbItemStatus $kbItemStatus)
    {
        abort_if(Gate::denies('kb_item_status_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-status.show', compact('kbItemStatus'));
    }
}
