<?php

namespace AdvisingApp\Engagement\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Models\Engagement;
use AdvisingApp\Notification\Enums\NotificationChannel;
use AdvisingApp\Notification\Models\EmailMessageEvent;
use AdvisingApp\Notification\Models\SmsMessageEvent;
use App\Filament\Clusters\UnifiedInbox;
use App\Infolists\Components\EngagementBody;
use App\Models\User;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\Tabs\Tab;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Locked;

class ViewEngagement extends Page
{
    protected static string $view = 'engagement::filament.pages.view-engagement';

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

    public static function getRoutePath(): string
    {
        return 'sent-items/{record}';
    }

    public function getTitle(): string
    {
        return strip_tags($this->record->getSubjectMarkdown());
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
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
