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
use AdvisingApp\Engagement\Enums\EngagementResponseType;
use AdvisingApp\Engagement\Filament\Actions\SendEngagementAction;
use AdvisingApp\Engagement\Models\EngagementResponse;
use AdvisingApp\Prospect\Filament\Resources\ProspectResource;
use AdvisingApp\Prospect\Models\Prospect;
use AdvisingApp\StudentDataModel\Filament\Resources\StudentResource;
use AdvisingApp\StudentDataModel\Models\Student;
use App\Filament\Clusters\UnifiedInbox;
use App\Models\User;
use Filament\Actions\ViewAction;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class Inbox extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'engagement::filament.pages.inbox';

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
                TextColumn::make('sender_type')
                    ->label('Relation')
                    ->formatStateUsing(fn (EngagementResponse $record) => ucwords($record->sender_type))
                    ->sortable(),
                TextColumn::make('from')
                    ->state(function (EngagementResponse $record): ?string {
                        return (($record->sender instanceof Student) || ($record->sender instanceof Prospect))
                            ? $record->sender->full_name
                            : null;
                    })
                    ->url(function (EngagementResponse $record) {
                        if ($record->sender instanceof Student) {
                            return StudentResource::getUrl('view', [
                                'record' => $record->sender,
                            ]);
                        }

                        if ($record->sender instanceof Prospect) {
                            return ProspectResource::getUrl('view', [
                                'record' => $record->sender,
                            ]);
                        }

                        return null;
                    }),
                TextColumn::make('type')
                    ->formatStateUsing(fn (EngagementResponse $record) => match ($record->type) {
                        EngagementResponseType::Email => 'Email',
                        EngagementResponseType::Sms => 'Text',
                    })
                    ->sortable(),
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
            ->recordActions([
                ViewAction::make()
                    ->url(fn (EngagementResponse $record): string => ViewEngagementResponse::getUrl(['record' => $record])),
            ])
            ->filters([
                SelectFilter::make('sender_type')
                    ->label('Relation')
                    ->options([
                        'student' => 'Student',
                        'prospect' => 'Prospect',
                    ]),
                SelectFilter::make('type')
                    ->options([
                        EngagementResponseType::Email->value => 'Email',
                        EngagementResponseType::Sms->value => 'Text',
                    ]),
            ])
            ->recordUrl(fn (EngagementResponse $record): string => ViewEngagementResponse::getUrl(['record' => $record]))
            ->defaultSort('sent_at', 'desc')
            ->emptyStateHeading('No Engagements yet.');
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return [
            parent::getNavigationItems()[0]
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getNavigationItemActiveRoutePattern(), ViewEngagementResponse::getNavigationItemActiveRoutePattern())),
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
