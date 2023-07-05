<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecordProgramItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecordProgramItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('record_program_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.record-program-item.index');
    }

    public function show(RecordProgramItem $recordProgramItem)
    {
        abort_if(Gate::denies('record_program_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.record-program-item.show', compact('recordProgramItem'));
    }
}
