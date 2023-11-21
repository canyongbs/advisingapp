<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Form\Http\Controllers;

use Illuminate\Support\Str;
use Assist\Form\Models\Form;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Filament\Support\Colors\Color;
use Illuminate\Support\Facades\URL;
use App\Http\Controllers\Controller;
use Assist\Form\Models\FormSubmission;
use Illuminate\Support\Facades\Validator;
use Assist\Form\Actions\GenerateFormKitSchema;
use Symfony\Component\HttpFoundation\Response;
use Assist\Form\Actions\GenerateFormValidation;
use Assist\Form\Actions\ResolveSubmissionAuthorFromEmail;
use Assist\Form\Filament\Blocks\EducatableEmailFormFieldBlock;

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

    public function store(Request $request, GenerateFormValidation $generateValidation, ResolveSubmissionAuthorFromEmail $resolveSubmissionAuthorFromEmail, Form $form): JsonResponse
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

        /** @var FormSubmission $submission */
        $submission = $form->submissions()->create();

        $data = $validator->validated();

        if ($form->is_wizard) {
            foreach ($form->steps as $step) {
                $stepFields = $step->fields()->pluck('type', 'id')->all();

                foreach ($data[$step->label] as $fieldId => $response) {
                    $submission->fields()->attach(
                        $fieldId,
                        ['id' => Str::orderedUuid(), 'response' => $response],
                    );

                    if ($submission->author) {
                        continue;
                    }

                    if ($stepFields[$fieldId] !== EducatableEmailFormFieldBlock::type()) {
                        continue;
                    }

                    $author = $resolveSubmissionAuthorFromEmail($response);

                    if (! $author) {
                        continue;
                    }

                    $submission->author()->associate($author);
                }
            }
        } else {
            $formFields = $form->fields()->pluck('type', 'id')->all();

            foreach ($data as $fieldId => $response) {
                $submission->fields()->attach(
                    $fieldId,
                    ['id' => Str::orderedUuid(), 'response' => $response],
                );

                if ($submission->author) {
                    continue;
                }

                if ($formFields[$fieldId] !== EducatableEmailFormFieldBlock::type()) {
                    continue;
                }

                $author = $resolveSubmissionAuthorFromEmail($response);

                if (! $author) {
                    continue;
                }

                $submission->author()->associate($author);
            }
        }

        $submission->save();

        return response()->json(
            [
                'message' => 'Form submitted successfully.',
            ]
        );
    }
}
