<?php

namespace AdvisingApp\Engagement\Filament\Pages;

use AdvisingApp\Authorization\Enums\LicenseType;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Clusters\UnifiedInbox;
use App\Models\User;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Page;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class Inbox extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $view = 'advising-engagement.filament.pages.inbox';

    protected static ?string $cluster = UnifiedInbox::class;

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

    public function table(Table $table): Table
    {
        return $table
            ->query(
                EngagementResponse::query()
            )
            ->columns([
                TextColumn::make('direction')
                    ->state('Inbound')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('from')
                    ->state(function (EngagementResponse $record): ?string {
                        return (($record->sender instanceof Student) || ($record->sender instanceof Prospect))
                            ? $record->sender->full_name
                            : null;
                    }),
                TextColumn::make('subject')
                    ->description(
                        fn (EngagementResponse $record): ?string => filled($body = $record->getBodyMarkdown())
                            ? Str::limit(strip_tags($body), 50)
                            : null
                    )
                    ->searchable(['subject', 'content']),
                TextColumn::make('sent_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->modalHeading('View Engagement Response')
                    ->infolist([
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
                    ]),
            ])
            ->recordAction('view')
            ->defaultSort('sent_at', 'desc')
            ->emptyStateHeading('No Engagements yet.');
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
