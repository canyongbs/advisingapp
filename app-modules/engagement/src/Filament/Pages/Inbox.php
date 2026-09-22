<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS Inc. All rights reserved.

    Advising App® is licensed under the Elastic License 2.0. For more details,
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
    - Canyon GBS Inc. respects the intellectual property rights of others and expects the
      same in return. Canyon GBS® and Advising App® are registered trademarks of
      Canyon GBS Inc., and we are committed to enforcing and protecting our trademarks
      vigorously.
    - The software solution, including services, infrastructure, and code, is offered as a
      Software as a Service (SaaS) by Canyon GBS Inc.
    - Use of this software implies agreement to the license terms and conditions as stated
      in the Elastic License 2.0.

    For more information or inquiries please visit our website at
    https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace AdvisingApp\Engagement\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Livewire\InboxTable;
use AdvisingApp\Engagement\Livewire\SentItemsTable;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Engagement\Models\EngagementResponse;
use App\Enums\NavigationGroup;
use App\Models\User;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Schemas\Components\Livewire;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Livewire\Attributes\Url;
use UnitEnum;

class Inbox extends Page
{
    protected static string | UnitEnum | null $navigationGroup = NavigationGroup::Crm;

    protected static ?string $navigationLabel = 'Unified Inbox';

    protected static ?string $title = 'Unified Inbox';

    protected static ?int $navigationSort = 10;

    #[Url(as: 'tab')]
    public string $activeTab = 'inbox';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        assert($user instanceof User);

        if (! $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])) {
            return false;
        }

        // These authorization checks have been preserved from the original message center.
        return ($user->can('viewAny', EngagementResponse::class) && $user->can('engagement_response.*.view'))
            || ($user->can('viewAny', Engagement::class) && $user->can('engagement.*.view'));
    }

    public function mount(): void
    {
        if ($this->isTabVisible($this->activeTab)) {
            return;
        }

        $this->activeTab = collect(['inbox', 'sent-items'])
            ->first(fn (string $tab): bool => $this->isTabVisible($tab)) ?? 'inbox';
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make()
                ->livewireProperty('activeTab')
                ->tabs([
                    'inbox' => Tab::make('Inbox')
                        ->visible(fn (): bool => $this->isTabVisible('inbox'))
                        ->schema([
                            Livewire::make(InboxTable::class),
                        ]),
                    'sent-items' => Tab::make('Sent Items')
                        ->visible(fn (): bool => $this->isTabVisible('sent-items'))
                        ->schema([
                            Livewire::make(SentItemsTable::class),
                        ]),
                ]),
        ]);
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return [
            parent::getNavigationItems()[0]
                ->isActiveWhen(fn (): bool => request()->routeIs(
                    static::getNavigationItemActiveRoutePattern(),
                    ViewEngagementResponse::getNavigationItemActiveRoutePattern(),
                    ViewEngagement::getNavigationItemActiveRoutePattern(),
                )),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            SendEngagementAction::make()
                ->label('New')
                ->icon(null),
        ];
    }

    protected function isTabVisible(string $tab): bool
    {
        $user = auth()->user();

        assert($user instanceof User);

        if (! $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])) {
            return false;
        }

        return match ($tab) {
            'inbox' => $user->can('viewAny', EngagementResponse::class) && $user->can('engagement_response.*.view'),
            'sent-items' => $user->can('viewAny', Engagement::class) && $user->can('engagement.*.view'),
            default => false,
        };
    }
}
