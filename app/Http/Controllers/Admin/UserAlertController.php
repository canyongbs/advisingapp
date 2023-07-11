<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\UserAlert;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class UserAlertController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('user_alert_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.user-alert.index');
    }

    public function create()
    {
        abort_if(Gate::denies('user_alert_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.user-alert.create');
    }

    public function edit(UserAlert $userAlert)
    {
        abort_if(Gate::denies('user_alert_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.user-alert.edit', compact('userAlert'));
    }

    public function show(UserAlert $userAlert)
    {
        abort_if(Gate::denies('user_alert_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $userAlert->load('users');

        return view('admin.user-alert.show', compact('userAlert'));
    }

    public function seen()
    {
        auth()->user()->alerts()
            ->newPivotStatement()
            ->where('user_id', auth()->id())
            ->whereNull('seen_at')
            ->update(['seen_at' => now()]);
    }
}
