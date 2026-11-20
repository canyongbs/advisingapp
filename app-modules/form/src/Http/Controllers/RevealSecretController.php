<?php

namespace AdvisingApp\Form\Http\Controllers;

use AdvisingApp\Application\Models\ApplicationFieldSubmission;
use AdvisingApp\Form\Actions\ResolveSubmissionSecretEncrypter;
use AdvisingApp\Form\Models\FormFieldSubmission;
use AdvisingApp\Form\Models\Secret;
use AdvisingApp\MeetingCenter\Models\EventRegistrationForm;
use AdvisingApp\MeetingCenter\Models\EventRegistrationFormFieldSubmission;
use App\Features\PasswordFormFieldFeature;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class RevealSecretController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        abort_unless(PasswordFormFieldFeature::active(), Response::HTTP_NOT_FOUND);

        $data = $request->validate([
            'secret_id' => ['required', 'uuid'],
        ]);

        $secret = Secret::query()->findOrFail($data['secret_id']);
        $fieldSubmission = $secret->related;

        abort_unless(
            $fieldSubmission instanceof FormFieldSubmission
            || $fieldSubmission instanceof ApplicationFieldSubmission
            || $fieldSubmission instanceof EventRegistrationFormFieldSubmission,
            Response::HTTP_NOT_FOUND,
        );

        $submission = $fieldSubmission->submission;
        $submissible = $submission->submissible;

        Gate::authorize('view', $submissible instanceof EventRegistrationForm ? $submissible->event : $submissible);

        return response()
            ->json([
                'value' => app(ResolveSubmissionSecretEncrypter::class)($submission)->decryptString($secret->value),
            ])
            ->header('Cache-Control', 'no-store, private');
    }
}
