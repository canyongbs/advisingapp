<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CaseItemStatus;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CaseItemStatusController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('case_item_status_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-status.index');
    }

    public function create()
    {
        abort_if(Gate::denies('case_item_status_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-status.create');
    }

    public function edit(CaseItemStatus $caseItemStatus)
    {
        abort_if(Gate::denies('case_item_status_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-status.edit', compact('caseItemStatus'));
    }

    public function show(CaseItemStatus $caseItemStatus)
    {
        abort_if(Gate::denies('case_item_status_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-status.show', compact('caseItemStatus'));
    }
}
