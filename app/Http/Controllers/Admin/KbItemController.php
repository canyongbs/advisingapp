<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\KbItem;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class KbItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('kb_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('kb_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item.create');
    }

    public function edit(KbItem $kbItem)
    {
        abort_if(Gate::denies('kb_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item.edit', compact('kbItem'));
    }

    public function show(KbItem $kbItem)
    {
        abort_if(Gate::denies('kb_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $kbItem->load('quality', 'status', 'category', 'institution');

        return view('admin.kb-item.show', compact('kbItem'));
    }
}
