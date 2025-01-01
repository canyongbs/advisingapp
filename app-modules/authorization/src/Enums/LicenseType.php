<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

    Advising App™ is licensed under the Elastic License 2.0. For more details,
    see https://github.com/canyongbs/advisingapp/blob/main/LICENSE.

    Notice:

    - You may not provide the software to third parties as a hosted or managed
      service, where the service provides users with access to any substantial set of
      the features or functionality of the software.
    - You may not move, change, disable, or circumvent the license key functionality
      in the software, and you may not remove or obscure any functionality in the
      software that is protected by the license key.
    - You may not alter, remove, or obscure any licensing, copyright, or other notices
      of the licensor in the software. Any use of the licensor’s trademarks is subject
      to applicable law.
    - Canyon GBS LLC respects the intellectual property rights of others and expects the
      same in return. Canyon GBS™ and Advising App™ are registered trademarks of
      Canyon GBS LLC, and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS LLC.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Authorization\Enums;

use AdvisingApp\Authorization\Models\License;
use App\Models\Authenticatable;
use App\Settings\LicenseSettings;
use Filament\Support\Contracts\HasLabel;

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
        return $this->getAvailableSeats() > 0;
    }

    public function isLicensable(): bool
    {
        return $this->getSeats() > 0;
    }

    public function getSeats(): int
    {
        $licenseSettings = app(LicenseSettings::class);

        return match ($this) {
            LicenseType::ConversationalAi => $licenseSettings->data->limits->conversationalAiSeats,
            LicenseType::RetentionCrm => $licenseSettings->data->limits->retentionCrmSeats,
            LicenseType::RecruitmentCrm => $licenseSettings->data->limits->recruitmentCrmSeats,
        };
    }

    public function getSeatsInUse(): int
    {
        return License::query()
            ->whereDoesntHave('user', function ($query) {
                $query->role(Authenticatable::SUPER_ADMIN_ROLE);
            })
            ->where('type', $this)
            ->count();
    }

    public function getAvailableSeats(): int
    {
        return max($this->getSeats() - $this->getSeatsInUse(), 0);
    }
}
