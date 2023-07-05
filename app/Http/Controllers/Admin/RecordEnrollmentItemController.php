<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RecordEnrollmentItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RecordEnrollmentItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('record_enrollment_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.record-enrollment-item.index');
    }

    public function show(RecordEnrollmentItem $recordEnrollmentItem)
    {
        abort_if(Gate::denies('record_enrollment_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.record-enrollment-item.show', compact('recordEnrollmentItem'));
    }
}
