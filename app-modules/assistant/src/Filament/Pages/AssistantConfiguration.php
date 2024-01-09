<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\Assistant\Filament\Pages;

use App\Filament\Pages\ArtificialIntelligence;
use Filament\Pages\Page;
use Filament\Navigation\NavigationItem;
use AdvisingApp\Consent\Filament\Resources\ConsentAgreementResource\Pages\ListConsentAgreements;

class AssistantConfiguration extends Page
{
    protected static string $view = 'assistant::filament.pages.assistant-configuration';

    protected static ?string $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?string $navigationLabel = 'AI Settings';

    protected static ?int $navigationSort = 10;

    protected static ?string $modelLabel = 'Artificial Intelligence';

    protected static ?string $pluralModelLabel = 'Artificial Intelligence';

    protected static ?string $title = 'Artificial Intelligence';

    protected static ?string $cluster = ArtificialIntelligence::class;

    public function getBreadcrumbs(): array
    {
        return [
            $this::getUrl() => 'Artificial Intelligence',
        ];
    }

    public static function shouldRegisterNavigation(): bool
    {
        return collect((new AssistantConfiguration())->getSubNavigation())
            ->filter(function (NavigationItem $item) {
                return $item->isVisible();
            })->isNotEmpty();
    }

    public function mount(): void
    {
        /** @var NavigationItem $firstNavItem */
        $firstNavItem = collect($this->getSubNavigation())->first(function (NavigationItem $item) {
            return $item->isVisible();
        });

        if (is_null($firstNavItem)) {
            abort(403);
        }

        redirect($firstNavItem->getUrl());
    }

    public static function getNavigationItems(): array
    {
        $item = parent::getNavigationItems()[0];

        $item->isActiveWhen(function (): bool {
            $subItems = (new AssistantConfiguration())->getSubNavigation();

            foreach ($subItems as $subItem) {
                if (str(request()->fullUrl())->contains(str($subItem->getUrl())->after('/'))) {
                    return true;
                }
            }

            return request()->routeIs(static::getRouteName());
        });

        return [$item];
    }

    public function getSubNavigation(): array
    {
        return $this->generateNavigationItems(
            [
                ListConsentAgreements::class,
                ManageAiSettings::class,
            ]
        );
    }
}
