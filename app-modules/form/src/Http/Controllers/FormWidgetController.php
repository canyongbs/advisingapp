<?php

namespace Assist\Form\Http\Controllers;

use Assist\Form\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Assist\Form\Actions\GenerateFormKitSchema;
use Illuminate\Support\Str;
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
                'primary_color' => Color::all()[$form->primary_color ?? 'blue'],
                'rounding' => $form->rounding,
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

        $submission = $form->submissions()->create();

        foreach ($validator->validated() as $fieldId => $response) {
            $submission->fields()->attach(
                $fieldId,
                ['id' => Str::orderedUuid(), 'response' => $response],
            );
        }

        return response()->json(
            [
                'message' => 'Form submitted successfully.',
            ]
        );
    }
}
