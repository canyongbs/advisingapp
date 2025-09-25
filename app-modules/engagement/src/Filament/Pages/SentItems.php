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
use AdvisingApp\Engagement\Enums\EngagementDisplayStatus;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Models\Engagement;
use App\Filament\Clusters\UnifiedInbox;
use App\Models\User;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class SentItems extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationLabel = 'Sent Items';

    protected static string $view = 'engagement::filament.pages.sent-items';

    protected static ?string $cluster = UnifiedInbox::class;

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

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Engagement::query()->whereHas('recipient')
            )
            ->columns([
                TextColumn::make('direction')
                    ->state('Outbound')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->badge(),
                TextColumn::make('status')
                    ->badge()
                    ->state(fn (Engagement $record) => EngagementDisplayStatus::getStatus($record)),
                TextColumn::make('recipient_type')
                    ->label('Relation')
                    ->formatStateUsing(fn (Engagement $record) => ucwords($record->recipient_type)),
                TextColumn::make('user.name')
                    ->label('From'),
                TextColumn::make('recipient.full_name')
                    ->label('To'),
                TextColumn::make('subject')
                    ->description(
                        fn (Engagement $record): ?string => filled($body = $record->getBodyMarkdown())
                            ? Str::limit(strip_tags($body), 50)
                            : null
                    )
                    ->state(fn (Engagement $record): string => strip_tags($record->getSubjectMarkdown()))
                    ->searchable(['subject', 'body']),
                TextColumn::make('dispatched_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                ViewAction::make()
                    ->url(fn (Engagement $record): string => ViewEngagement::getUrl(['record' => $record])),
            ])
            ->recordUrl(fn (Engagement $record): string => ViewEngagement::getUrl(['record' => $record]))
            ->defaultSort('dispatched_at', 'desc')
            ->emptyStateHeading('No Engagements yet.');
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return [
            parent::getNavigationItems()[0]
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getNavigationItemActiveRoutePattern(), ViewEngagement::getNavigationItemActiveRoutePattern())),
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
}
