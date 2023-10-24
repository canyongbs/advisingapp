<?php

namespace Assist\Form\Http\Controllers;

use Assist\Form\Models\Form;
use Illuminate\Http\Request;
use Assist\Form\Models\FormField;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use Assist\Form\Actions\GenerateFormValidation;
use Assist\Form\Actions\GenerateFormFieldFormKitSchema;

class FormWidgetController extends Controller
{
    public function view(Request $request, Form $form): JsonResponse
    {
        return response()->json(
            [
                'name' => $form->name,
                'description' => $form->description,
                'schema' => $form->fields->map(fn (FormField $field) => resolve(GenerateFormFieldFormKitSchema::class)->handle($field)),
            ]
        );
    }

    public function store(Request $request, Form $form): JsonResponse
    {
        $validator = Validator::make(
            $request->all(),
            resolve(GenerateFormValidation::class)->handle($form)
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
