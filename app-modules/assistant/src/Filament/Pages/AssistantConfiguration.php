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

namespace Assist\Assistant\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Navigation\NavigationItem;
use Assist\Consent\Filament\Resources\ConsentAgreementResource\Pages\ListConsentAgreements;

class AssistantConfiguration extends Page
{
    protected static string $view = 'assist.assistant.filament.pages.assistant-configuration';

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationLabel = 'Artificial Intelligence';

    protected static ?string $navigationGroup = 'Product Administration';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Artificial Intelligence';

    protected static ?string $pluralModelLabel = 'Artificial Intelligence';

    protected static ?string $title = 'Artificial Intelligence';

    public function getBreadcrumbs(): array
    {
        return [
            $this::getUrl() => 'Artificial Intelligence',
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        return $user->can(['assistant.access_ai_settings']) || ListConsentAgreements::shouldRegisterNavigation();
    }

    public function mount(): void
    {
        /** @var User $user */
        $user = auth()->user();

        abort_unless($user->can(['assistant.access_ai_settings']) || ListConsentAgreements::shouldRegisterNavigation(), 403);

        /** @var NavigationItem $firstNavItem */
        $firstNavItem = collect($this->getSubNavigation())->first(function (NavigationItem $item) {
            return $item->isVisible();
        });

        if (is_null($firstNavItem)) {
            abort(403);
        }

        redirect($firstNavItem->getUrl());
    }

    public function getSubNavigation(): array
    {
        $navigationItems = $this->generateNavigationItems(
            [
                ListConsentAgreements::class,
            ]
        );

        /** @var User $user */
        $user = auth()->user();

        if ($user->can(['assistant.access_ai_settings'])) {
            $navigationItems = [
                ...$navigationItems,
                ...ManageAiSettings::getNavigationItems(),
            ];
        }

        return $navigationItems;
    }
}
