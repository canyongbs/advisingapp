<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTrainingItem;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SupportTrainingItemController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('support_training_item_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-training-item.index');
    }

    public function create()
    {
        abort_if(Gate::denies('support_training_item_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-training-item.create');
    }

    public function edit(SupportTrainingItem $supportTrainingItem)
    {
        abort_if(Gate::denies('support_training_item_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-training-item.edit', compact('supportTrainingItem'));
    }

    public function show(SupportTrainingItem $supportTrainingItem)
    {
        abort_if(Gate::denies('support_training_item_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.support-training-item.show', compact('supportTrainingItem'));
    }
}
