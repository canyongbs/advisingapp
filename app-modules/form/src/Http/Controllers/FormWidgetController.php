<?php

namespace Assist\Form\Http\Controllers;

use Assist\Form\Models\Form;
use Illuminate\Http\Request;
use Assist\Form\Models\FormField;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Assist\Form\Actions\GenerateFormValidation;
use Assist\Form\Actions\GenerateFormFieldFormKitSchema;

class FormWidgetController extends Controller
{
    public function view(Request $request, Form $form): JsonResponse
    {
        // TODO: Move this out of the controller once we go beyond these simple fields and configurations.
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
        $validated = $request->validate(resolve(GenerateFormValidation::class)->handle($form));

        $form->submissions()->create([
            'content' => $validated,
        ]);

        return response()->json(
            [
                'message' => 'Form submitted successfully.',
            ]
        );
    }
}
