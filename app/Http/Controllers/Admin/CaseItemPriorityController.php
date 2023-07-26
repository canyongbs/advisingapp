<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use Assist\CaseModule\Models\CaseItemPriority;

class CaseItemPriorityController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('case_item_priority_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-priority.index');
    }

    public function create()
    {
        abort_if(Gate::denies('case_item_priority_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-priority.create');
    }

    public function edit(CaseItemPriority $caseItemPriority)
    {
        abort_if(Gate::denies('case_item_priority_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-priority.edit', compact('caseItemPriority'));
    }

    public function show(CaseItemPriority $caseItemPriority)
    {
        abort_if(Gate::denies('case_item_priority_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item-priority.show', compact('caseItemPriority'));
    }
}
