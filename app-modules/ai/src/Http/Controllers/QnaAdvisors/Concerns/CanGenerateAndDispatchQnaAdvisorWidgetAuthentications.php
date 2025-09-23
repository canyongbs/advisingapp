<?php

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors\Concerns;

use AdvisingApp\Ai\Models\QnaAdvisor;
use AdvisingApp\Portal\Enums\PortalType;
use AdvisingApp\Portal\Models\PortalAuthentication;
use AdvisingApp\Portal\Notifications\AuthenticatePortalNotification;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;

trait CanGenerateAndDispatchQnaAdvisorWidgetAuthentications
{
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
