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

namespace AdvisingApp\MeetingCenter\Filament\Resources\Events\Pages;

use AdvisingApp\MeetingCenter\Filament\Actions\InviteEventAttendeesAction;
use AdvisingApp\MeetingCenter\Filament\Actions\Table\ViewEventAttendeeAction;
use AdvisingApp\MeetingCenter\Filament\Resources\Events\EventResource;
use AdvisingApp\MeetingCenter\Models\EventAttendee;
use App\Features\ArchiveSubmissionsFeature;
use App\Filament\Tables\Columns\IdColumn;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ManageEventAttendees extends ManageRelatedRecords
{
    protected static string $resource = EventResource::class;

    // TODO: Obsolete when there is no table, remove from Filament
    protected static string $relationship = 'attendees';

    protected static ?string $navigationLabel = 'Attendees';

    protected static ?string $breadcrumb = 'Attendees';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query): Builder {
                if (ArchiveSubmissionsFeature::active()) {
                    /** @phpstan-ignore method.notFound */
                    return $query->withoutArchived();
                }

                return $query;
            })
            ->columns([
                IdColumn::make(),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('email'),
            ])
            ->headerActions([
            ])
            ->recordActions([
                ViewEventAttendeeAction::make(),
                ...(ArchiveSubmissionsFeature::active() ? [
                    Action::make('archive')
                        ->label('Archive')
                        ->icon('heroicon-o-archive-box')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Archive Attendee')
                        ->modalSubmitActionLabel('Archive')
                        ->authorize(fn (EventAttendee $record) => auth()->user()->can('archive', $record))
                        ->action(function (EventAttendee $record): void {
                            $record->archive();

                            Notification::make()
                                ->title('Attendee archived')
                                ->success()
                                ->send();
                        })
                        ->hidden(fn (EventAttendee $record): bool => $record->isArchived()),
                    Action::make('unarchive')
                        ->label('Unarchive')
                        ->icon('heroicon-o-archive-box-arrow-down')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Unarchive Attendee')
                        ->modalSubmitActionLabel('Unarchive')
                        ->authorize(fn (EventAttendee $record) => auth()->user()->can('unarchive', $record))
                        ->action(function (EventAttendee $record): void {
                            $record->unarchive();

                            Notification::make()
                                ->title('Attendee unarchived')
                                ->success()
                                ->send();
                        })
                        ->hidden(fn (EventAttendee $record): bool => ! $record->isArchived()),
                ] : []),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ...(ArchiveSubmissionsFeature::active() ? [
                        BulkAction::make('archive')
                            ->label('Archive')
                            ->icon('heroicon-o-archive-box')
                            ->color('warning')
                            ->requiresConfirmation()
                            ->modalHeading('Archive Attendees')
                            ->modalSubmitActionLabel('Archive')
                            ->authorize(fn () => auth()->user()->can('deleteAny', EventAttendee::class))
                            ->action(function (Collection $records): void {
                                /** @phpstan-ignore argument.type */
                                $records->each(function (EventAttendee $record): void {
                                    $record->archive();
                                });

                                Notification::make()
                                    ->title('Attendees archived')
                                    ->success()
                                    ->send();
                            }),
                    ] : []),
                ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            InviteEventAttendeesAction::make(),
        ];
    }
}
