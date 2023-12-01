<?php

namespace App\Enums;

use App\Settings\LicenseSettings;
use Illuminate\Support\Facades\Gate;

enum Feature: string
{
    case OnlineAdmissions = 'online-admissions';

    case RealtimeChat = 'realtime-chat';

    case DynamicForms = 'dynamic-forms';

    case ConductSurveys = 'conduct-surveys';

    case PersonalAssistant = 'personal-assistant';

    case ServiceManagement = 'service-management';

    case KnowledgeManagement = 'knowledge-management';

    case StudentAndProspectPortal = 'student-and-prospect-portal';

    public function generateGate(): void
    {
        // If features are added that are not based on a License Addon we will need to update this
        Gate::define(
            $this->value,
            fn () => app(LicenseSettings::class)->data->addons->{str($this->value)->camel()}
        );
    }
}
