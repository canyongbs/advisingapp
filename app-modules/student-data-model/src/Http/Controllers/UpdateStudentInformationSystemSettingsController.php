<?php

namespace AdvisingApp\StudentDataModel\Http\Controllers;

use AdvisingApp\StudentDataModel\Enums\SisSystem;
use AdvisingApp\StudentDataModel\Http\Requests\UpdateStudentInformationSystemSettingsRequest;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UpdateStudentInformationSystemSettingsController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(UpdateStudentInformationSystemSettingsRequest $request)
    {
        $settings = app(StudentInformationSystemSettings::class);

        $settings->is_enabled = $request->is_enabled;
        $settings->sis_system = SisSystem::parse($request->sis_system);

        $settings->save();

        return response()->json();
    }
}
