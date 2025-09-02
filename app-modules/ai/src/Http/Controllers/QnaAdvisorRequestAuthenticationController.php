<?php

namespace AdvisingApp\Ai\Http\Controllers;

use AdvisingApp\Ai\Http\Requests\QnaAdvisorRequestAuthenticationRequest;
use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Portal\Enums\PortalType;
use AdvisingApp\Portal\Models\PortalAuthentication;
use AdvisingApp\Portal\Notifications\AuthenticatePortalNotification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Actions\ResolveEducatableFromEmail;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class QnaAdvisorRequestAuthenticationController
{
    public function __invoke(QnaAdvisorRequestAuthenticationRequest $request, QnaAdvisor $advisor, ResolveEducatableFromEmail $resolveEducatableFromEmail): JsonResponse
    {
        $email = $request->safe()['email'];

        $educatable = $resolveEducatableFromEmail($email);

        throw_if(! $educatable, ValidationException::withMessages([
            'email' => 'A student or prospect with that email address could not be found. Please contact your system administrator.',
        ]));

        $authenticationUrl = $this->createPortalAuthentication($educatable, $advisor);

        return response()->json([
            'message' => "We've sent an authentication code to {$email}.",
            'authentication_url' => $authenticationUrl,
        ]);
    }

    protected function createPortalAuthentication(Prospect|Student $educatable, QnaAdvisor $advisor): string
    {
        $code = random_int(100000, 999999);

        $authentication = new PortalAuthentication();
        $authentication->portal_type = PortalType::QnaAdvisorWidget;
        $authentication->code = Hash::make((string) $code);

        $authentication->educatable()->associate($educatable);

        $authentication->save();

        $educatable->notify(new AuthenticatePortalNotification($authentication, $code));

        return URL::signedRoute(
            name: 'ai.qna-advisors.authentication.confirm',
            parameters: [
                'advisor' => $advisor,
                'authentication' => $authentication,
            ],
        );
    }
}
