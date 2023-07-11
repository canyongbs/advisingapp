<?php

namespace App\Http\Controllers\Admin;

use Gate;
use Illuminate\Http\Response;
use App\Models\SupportFeedbackItem;
use App\Http\Controllers\Controller;

class SupportFeedbackItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('support_feedback_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-feedback-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('support_feedback_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-feedback-item.create');
    }

    public function edit(SupportFeedbackItem $supportFeedbackItem)
    {
        abort_if(Gate::denies('support_feedback_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-feedback-item.edit', compact('supportFeedbackItem'));
    }

    public function show(SupportFeedbackItem $supportFeedbackItem)
    {
        abort_if(Gate::denies('support_feedback_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-feedback-item.show', compact('supportFeedbackItem'));
    }
}
