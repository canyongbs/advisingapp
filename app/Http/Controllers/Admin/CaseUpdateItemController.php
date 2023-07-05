<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseUpdateItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CaseUpdateItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('case_update_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-update-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('case_update_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-update-item.create');
    }

    public function show(CaseUpdateItem $caseUpdateItem)
    {
        abort_if(Gate::denies('case_update_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $caseUpdateItem->load('student', 'case');

        return view('admin.case-update-item.show', compact('caseUpdateItem'));
    }
}
