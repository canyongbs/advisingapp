<?php

namespace Assist\Form\Http\Controllers;

use Assist\Form\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class FormWidgetController extends Controller
{
    public function view(Request $request, Form $form): JsonResponse
    {
        if (! $form->embed_enabled) {
            return response()->json(['error' => 'Embedding is not enabled for this form.'], 403);
        }

        $referer = $request->headers->get('referer');

        if (! $referer) {
            return response()->json(['error' => 'Missing referer header.'], 400);
        }

        $allowedDomains = collect($form->allowed_domains ?? [])->merge([parse_url(config('app.url'))['host']]);

        $referer = parse_url($referer)['host'];

        if (! $allowedDomains->contains($referer)) {
            return response()->json(['error' => 'Referer not allowed. Domain must be added to allowed domains list'], 403);
        }

        // TODO: Move this out of the controller once we go beyond these simple fields and configurations.
        return response()->json(
            [
                'name' => $form->name,
                'description' => $form->description,
                'schema' => $form->fields->map(function ($field) {
                    return match ($field['type']) {
                        'text_input' => (object) [
                            '$formkit' => 'text',
                            'label' => $field['label'],
                            'name' => $field['key'],
                            'required' => $field['required'],
                        ],
                        'text_area' => (object) [
                            '$formkit' => 'textarea',
                            'label' => $field['label'],
                            'name' => $field['key'],
                            'required' => $field['required'],
                        ],
                        'select' => (object) [
                            '$formkit' => 'select',
                            'label' => $field['label'],
                            'name' => $field['key'],
                            'required' => $field['required'],
                            'options' => $field['config']['options'],
                        ],
                    };
                })->toArray(),
            ]
        );
    }
}
