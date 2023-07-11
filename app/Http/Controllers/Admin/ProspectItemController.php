<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\ProspectItem;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\WithCSVImport;

class ProspectItemController extends Controller
{
    use WithCSVImport;

    public function __construct()
    {
        $this->csvImportModel = ProspectItem::class;
    }

    public function index()
    {
        abort_if(Gate::denies('prospect_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('prospect_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-item.create');
    }

    public function edit(ProspectItem $prospectItem)
    {
        abort_if(Gate::denies('prospect_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.prospect-item.edit', compact('prospectItem'));
    }

    public function show(ProspectItem $prospectItem)
    {
        abort_if(Gate::denies('prospect_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $prospectItem->load('status', 'source', 'assignedTo', 'createdBy');

        return view('admin.prospect-item.show', compact('prospectItem'));
    }
}
