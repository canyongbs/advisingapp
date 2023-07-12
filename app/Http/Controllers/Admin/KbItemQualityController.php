<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\KbItemQuality;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class KbItemQualityController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('kb_item_quality_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-quality.index');
    }

    public function create()
    {
        abort_if(Gate::denies('kb_item_quality_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-quality.create');
    }

    public function edit(KbItemQuality $kbItemQuality)
    {
        abort_if(Gate::denies('kb_item_quality_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-quality.edit', compact('kbItemQuality'));
    }

    public function show(KbItemQuality $kbItemQuality)
    {
        abort_if(Gate::denies('kb_item_quality_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-quality.show', compact('kbItemQuality'));
    }
}
