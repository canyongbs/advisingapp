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

namespace AdvisingApp\Engagement\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Models\EngagementResponse;
use App\Filament\Clusters\UnifiedInbox;
use App\Models\User;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Livewire\Attributes\Locked;

class ViewEngagementResponse extends Page
{
    protected static string $view = 'engagement::filament.pages.view-engagement-response';

    protected static ?string $cluster = UnifiedInbox::class;

    protected static bool $shouldRegisterNavigation = false;

    #[Locked]
    public EngagementResponse $record;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        assert($user instanceof User);

        if (! $user->can('viewAny', EngagementResponse::class)) {
            return false;
        }

        if (! $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])) {
            return false;
        }

        // This authorization check has been preserved from the original message center.
        return $user->can('engagement_response.*.view');
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return static::getCluster()::unshiftClusterBreadcrumbs([
            Inbox::getUrl() => 'Inbox',
        ]);
    }

    public static function getRoutePath(): string
    {
        return 'inbox/{record}';
    }

    public function getTitle(): string
    {
        return $this->record->subject;
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Split::make([
                    Section::make([
                        TextEntry::make('subject')
                            ->columnSpanFull(),
                        TextEntry::make('content')
                            ->columnSpanFull(),
                    ]),
                    Section::make([
                        TextEntry::make('sent_at')
                            ->dateTime(),
                    ])->grow(false),
                ])
                    ->from('md')
                    ->columnSpanFull(),
            ])
            ->record($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            SendEngagementAction::make()
                ->label('New')
                ->icon(null),
        ];
    }
}
