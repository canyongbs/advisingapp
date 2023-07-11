<?php

namespace App\Http\Controllers\Admin;

use Gate;
use App\Models\Institution;
use Illuminate\Http\Response;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\WithCSVImport;

class InstitutionController extends Controller
{
    use WithCSVImport;

    public function __construct()
    {
        $this->csvImportModel = Institution::class;
    }

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
}
