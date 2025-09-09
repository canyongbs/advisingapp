<?php

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
