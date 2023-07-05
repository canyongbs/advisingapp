<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\WithCSVImport;
use App\Models\CaseItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CaseItemController extends Controller
{
    use WithCSVImport;

    public function index()
    {
        abort_if(Gate::denies('case_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('case_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item.create');
    }

    public function edit(CaseItem $caseItem)
    {
        abort_if(Gate::denies('case_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.case-item.edit', compact('caseItem'));
    }

    public function show(CaseItem $caseItem)
    {
        abort_if(Gate::denies('case_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $caseItem->load('student', 'institution', 'state', 'type', 'priority', 'assignedTo', 'createdBy');

        return view('admin.case-item.show', compact('caseItem'));
    }

    public function __construct()
    {
        $this->csvImportModel = CaseItem::class;
    }
}
