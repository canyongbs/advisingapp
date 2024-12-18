<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\Concerns;

use App\Settings\DisplaySettings;
use Illuminate\Contracts\View\View;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\StudentDataModel\Settings\StudentInformationSystemSettings;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource\Pages\ViewProspect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource\Pages\ViewStudent;

trait HasProspectHeader
{
    public function getHeader(): ?View
    {
        $sisSettings = app(StudentInformationSystemSettings::class);

        $prospect = $this->getRecord();
        $prospectName = filled($prospect->full_name)
            ? $prospect->full_name
            : "{$prospect->first_name} {$prospect->last_name}";

        return view('student-data-model::filament.resources.educatable-resource.view-educatable.header', [
            'actions' => $this->getCachedHeaderActions(),
            'backButtonLabel' => 'Back to prospect',
            'backButtonUrl' => $this instanceof ViewProspect
                ? null
                : ProspectResource::getUrl('view', ['record' => $this->getRecord()]),
            'badges' => [
                ...($prospect->firstgen ? ['First Gen'] : []),
                ...($prospect->dual ? ['Dual'] : []),
                ...($prospect->sap ? ['SAP'] : []),
                ...(filled($prospect->dfw) ? ["DFW {$prospect->dfw->format('m/d/Y')}"] : []),
            ],
            'breadcrumbs' => $this->getBreadcrumbs(),
            'details' => [
                ['Prospect', 'heroicon-m-magnifying-glass-circle'],
                ...(filled($prospect->preferred) ? [["Goes by \"{$prospect->preferred}\"", 'heroicon-m-heart']] : []),
                ...(filled($prospect->phone) ? [[$prospect->phone, 'heroicon-m-phone']] : []),
                ...(filled($prospect->email) ? [[$prospect->email, 'heroicon-m-envelope']] : []),
                ...(filled($prospect->hsgrad) ? [[$prospect->hsgrad, 'heroicon-m-building-library']] : []),
            ],
            'hasSisSystem' => $sisSettings->is_enabled && $sisSettings->sis_system,
            'educatable' => $prospect,
            'educatableInitials' => str($prospectName)
                ->trim()
                ->explode(' ')
                ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
                ->join(' '),
            'educatableName' => $prospectName,
            'timezone' => app(DisplaySettings::class)->getTimezone(),
        ]);
    }
}