<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EngagementStudentFile;
use Gate;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EngagementStudentFileController extends Controller
{
    public function index()
    {
        abort_if(Gate::denies('engagement_student_file_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-student-file.index');
    }

    public function create()
    {
        abort_if(Gate::denies('engagement_student_file_create'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-student-file.create');
    }

    public function edit(EngagementStudentFile $engagementStudentFile)
    {
        abort_if(Gate::denies('engagement_student_file_edit'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return view('admin.engagement-student-file.edit', compact('engagementStudentFile'));
    }

    public function show(EngagementStudentFile $engagementStudentFile)
    {
        abort_if(Gate::denies('engagement_student_file_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $engagementStudentFile->load('student');

        return view('admin.engagement-student-file.show', compact('engagementStudentFile'));
    }

    public function storeMedia(Request $request)
    {
        abort_if(Gate::none(['engagement_student_file_create', 'engagement_student_file_edit']), Response::HTTP_FORBIDDEN, '403 Forbidden');

        if ($request->has('size')) {
            $this->validate($request, [
                'file' => 'max:' . $request->input('size') * 1024,
            ]);
        }

        $model                     = new EngagementStudentFile();
        $model->id                 = $request->input('model_id', 0);
        $model->exists             = true;
        $media                     = $model->addMediaFromRequest('file')->toMediaCollection($request->input('collection_name'));
        $media->wasRecentlyCreated = true;

        return response()->json(compact('media'), Response::HTTP_CREATED);
    }
}
