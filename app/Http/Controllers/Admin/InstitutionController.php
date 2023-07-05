<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\WithCSVImport;
use App\Models\Institution;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InstitutionController extends Controller
{
    use WithCSVImport;

    public function index()
    {
        abort_if(Gate::denies('institution_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.institution.index');
    }

    public function create()
    {
        abort_if(Gate::denies('institution_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.institution.create');
    }

    public function edit(Institution $institution)
    {
        abort_if(Gate::denies('institution_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.institution.edit', compact('institution'));
    }

    public function show(Institution $institution)
    {
        abort_if(Gate::denies('institution_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.institution.show', compact('institution'));
    }

    public function __construct()
    {
        $this->csvImportModel = Institution::class;
    }
}
