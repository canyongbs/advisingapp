<?php

namespace Assist\Form\Http\Controllers;

use Assist\Form\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Assist\Form\Actions\GenerateFormKitSchema;
use Symfony\Component\HttpFoundation\Response;
use Assist\Form\Actions\GenerateFormValidation;

class FormWidgetController extends Controller
{
    public function view(GenerateFormKitSchema $generateSchema, Form $form): JsonResponse
    {
        return response()->json(
            [
                'name' => $form->name,
                'description' => $form->description,
                'submission_url' => URL::signedRoute('forms.submit', ['form' => $form]),
                'schema' => $generateSchema($form),
            ],
        );
    }

    public function store(Request $request, GenerateFormValidation $generateValidation, Form $form): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            $generateValidation($form)
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'errors' => (object) $validator->errors(),
                ],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $form->submissions()->create([
            'content' => $request->all(),
        ]);

        return response()->json(
            [
                'message' => 'Form submitted successfully.',
            ]
        );
    }
}
