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

use Filament\Panel;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Section;
use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\EmailMessageEvent;
use AdvisingApp\Notification\Models\SmsMessageEvent;
use App\Filament\Clusters\UnifiedInbox;
use App\Infolists\Components\EngagementBody;
use App\Models\User;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;

class ViewEngagement extends Page
{
    protected string $view = 'engagement::filament.pages.view-engagement';

    protected static ?string $cluster = UnifiedInbox::class;

    protected static bool $shouldRegisterNavigation = false;

    #[Locked]
    public Engagement $record;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        assert($user instanceof User);

        if (! $user->can('viewAny', Engagement::class)) {
            return false;
        }

        if (! $user->hasAnyLicense([LicenseType::RetentionCrm, LicenseType::RecruitmentCrm])) {
            return false;
        }

        // This authorization check has been preserved from the original message center.
        return $user->can('engagement.*.view');
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return static::getCluster()::unshiftClusterBreadcrumbs([
            SentItems::getUrl() => 'Sent Items',
        ]);
    }

    public static function getRoutePath(Panel $panel): string
    {
        return 'sent-items/{record}';
    }

    public function getTitle(): string
    {
        return strip_tags($this->record->getSubjectMarkdown()) ?: 'Sent Items';
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make()
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Content')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Created By'),
                                Fieldset::make('Content')
                                    ->schema([
                                        TextEntry::make('subject')
                                            ->state(function (Engagement $record): string {
                                                return strip_tags($record->getSubjectMarkdown());
                                            })
                                            ->visible(fn (Engagement $record): bool => $record->channel === NotificationChannel::Email)
                                            ->columnSpanFull(),
                                        EngagementBody::make('body')
                                            ->getStateUsing(fn (Engagement $record): HtmlString => $record->getBody())
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tab::make('Events')
                            ->schema([
                                RepeatableEntry::make('message_events')
                                    ->state(function (Engagement $record) {
                                        return match ($record->channel) {
                                            NotificationChannel::Email => $record->latestEmailMessage?->events()?->orderBy('occurred_at', 'desc')->get() ?? [],
                                            NotificationChannel::Sms => $record->latestSmsMessage?->events()?->orderBy('occurred_at', 'desc')->get() ?? [],
                                            default => [],
                                        };
                                    })
                                    ->schema([
                                        Section::make()
                                            ->schema([
                                                TextEntry::make('type')
                                                    ->getStateUsing(fn (EmailMessageEvent | SmsMessageEvent $record): string => $record->type->getLabel()),
                                                TextEntry::make('occured_at')
                                                    ->dateTime()
                                                    ->getStateUsing(fn (EmailMessageEvent| SmsMessageEvent $record): string => $record->occurred_at->format('Y-m-d H:i:s')),
                                            ])
                                            ->columns(),
                                    ])
                                    ->contained(false),
                            ]),
                    ]),
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
