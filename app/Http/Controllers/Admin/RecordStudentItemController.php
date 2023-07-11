<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Models\RecordStudentItem;
use App\Http\Controllers\Controller;

class RecordStudentItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('record_student_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.record-student-item.index');
    }

    public function show(RecordStudentItem $recordStudentItem)
    {
        abort_if(Gate::denies('record_student_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.record-student-item.show', compact('recordStudentItem'));
    }
}
