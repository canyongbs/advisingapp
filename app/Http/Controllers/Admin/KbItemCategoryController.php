<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Models\KbItemCategory;
use App\Http\Controllers\Controller;

class KbItemCategoryController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('kb_item_category_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-category.index');
    }

    public function create()
    {
        abort_if(Gate::denies('kb_item_category_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-category.create');
    }

    public function edit(KbItemCategory $kbItemCategory)
    {
        abort_if(Gate::denies('kb_item_category_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-category.edit', compact('kbItemCategory'));
    }

    public function show(KbItemCategory $kbItemCategory)
    {
        abort_if(Gate::denies('kb_item_category_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.kb-item-category.show', compact('kbItemCategory'));
    }
}
