<?php

namespace AdvisingApp\Authorization\Enums;

use App\Settings\LicenseSettings;
use Filament\Support\Contracts\HasLabel;
use AdvisingApp\Authorization\Models\License;

enum LicenseType: string implements HasLabel
{
    case ConversationalAi = 'conversational_ai';

    case RetentionCrm = 'retention_crm';

    case RecruitmentCrm = 'recruitment_crm';

    public function getLabel(): ?string
    {
        return match ($this) {
            LicenseType::ConversationalAi => 'Conversational AI',
            LicenseType::RetentionCrm => 'Retention CRM',
            LicenseType::RecruitmentCrm => 'Recruitment CRM',
        };
    }

    public function hasAvailableLicenses(): bool
    {
        $totalLicensesInUse = License::query()->where('type', $this)->count();

        $licenseSettings = app(LicenseSettings::class);

        $licenseLimit = match ($this) {
            LicenseType::ConversationalAi => $licenseSettings->data->limits->conversationalAiSeats,
            LicenseType::RetentionCrm => $licenseSettings->data->limits->retentionCrmSeats,
            LicenseType::RecruitmentCrm => $licenseSettings->data->limits->recruitmentCrmSeats,
        };

        return $totalLicensesInUse < $licenseLimit;
    }
}
