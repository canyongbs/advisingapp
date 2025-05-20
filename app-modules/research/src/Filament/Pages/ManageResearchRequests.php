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

namespace AdvisingApp\Research\Filament\Pages;

use App\Models\User;
use App\Enums\Feature;
use Filament\Pages\Page;
use App\Features\ResearchRequests;
use Illuminate\Support\Facades\Gate;
use Filament\Navigation\NavigationItem;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Ai\Settings\AiIntegrationsSettings;
use AdvisingApp\Research\Filament\Pages\ManageResearchRequests\Concerns\CanManageConsent;
use AdvisingApp\Research\Filament\Pages\ManageResearchRequests\Concerns\CanManageFolders;
use AdvisingApp\Research\Filament\Pages\ManageResearchRequests\Concerns\CanManageRequests;

class ManageResearchRequests extends Page
{
    use CanManageConsent;
    use CanManageFolders;
    use CanManageRequests;

    protected static string $view = 'research::filament.pages.manage-research-requests';

    protected static ?string $title = 'Research Requests';

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make('Research Requests')
                ->group('Artificial Intelligence')
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteName(), NewResearchRequest::getRouteName()))
                ->sort(30)
                ->url(static::getUrl()),
        ];
    }

    public static function canAccess(): bool
    {
        /** @var User $user */
        $user = auth()->user();

        if (! $user->hasLicense(LicenseType::ConversationalAi)) {
            return false;
        }

        if (! Gate::check(Feature::ResearchAdvisor->getGateName())) {
            return false;
        }

        if (blank(app(AiIntegrationsSettings::class)->jina_deepsearch_ai_api_key)) {
            return false;
        }

        return ResearchRequests::active() && $user->can(['research_advisor.view-any', 'research_advisor.*.view']);
    }
}
