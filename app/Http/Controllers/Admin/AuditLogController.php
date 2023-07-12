<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\AuditLog;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;

class AuditLogController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('audit_log_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.audit-log.index');
    }

    public function show(AuditLog $auditLog)
    {
        abort_if(Gate::denies('audit_log_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.audit-log.show', compact('auditLog'));
    }
}
