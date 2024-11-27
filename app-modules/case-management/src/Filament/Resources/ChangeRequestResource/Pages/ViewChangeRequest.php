<?php

/*
<COPYRIGHT>

    Copyright © 2016-2024, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\CaseManagement\Filament\Resources\ChangeRequestResource\Pages;

use Carbon\CarbonInterface;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Infolists\Infolist;
use Filament\Support\Enums\IconSize;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Components\RepeatableEntry;
use AdvisingApp\CaseManagement\Models\ChangeRequest;
use AdvisingApp\CaseManagement\Models\ChangeRequestStatus;
use AdvisingApp\CaseManagement\Models\Scopes\ClassifiedAs;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use AdvisingApp\CaseManagement\Enums\SystemChangeRequestClassification;
use AdvisingApp\CaseManagement\Filament\Resources\ChangeRequestResource;
use AdvisingApp\CaseManagement\Actions\ChangeRequest\ApproveChangeRequest;

class ViewChangeRequest extends ViewRecord
{
    protected static string $resource = ChangeRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Approval Status')
                    ->headerActions([
                        InfolistAction::make('approveChangeRequest')
                            ->requiresConfirmation()
                            ->hidden(fn (ChangeRequest $record) => $record->type()->withTrashed()->first()->number_of_required_approvals === 0 || $record->isNotNew())
                            ->disabled(fn (ChangeRequest $record) => $record->isNotNew() || ! $record->canBeApprovedBy(auth()->user()))
                            ->action(fn (ChangeRequest $record) => resolve(ApproveChangeRequest::class, ['changeRequest' => $record, 'user' => auth()->user()])->handle()),
                    ])
                    ->icon(fn (ChangeRequest $record) => $record->getIcon())
                    ->iconColor(fn (ChangeRequest $record) => $record->getIconColor())
                    ->iconSize(IconSize::Large)
                    ->schema([
                        TextEntry::make('type.number_of_required_approvals')
                            ->label('Approvals Needed')
                            ->columnSpan(2),
                        RepeatableEntry::make('approvals')
                            ->label('Approved By')
                            ->hidden(fn (ChangeRequest $record) => $record->type()->withTrashed()->first()->number_of_required_approvals === 0)
                            ->schema([
                                TextEntry::make('user')
                                    ->formatStateUsing(fn ($state) => $state->name)
                                    ->hiddenLabel()
                                    ->url(fn ($state) => UserResource::getUrl('view', ['record' => $state]))
                                    ->color('primary'),
                            ]),
                    ]),
                Section::make('Change Request Details')
                    ->schema([
                        TextEntry::make('title')
                            ->columnSpan(3),
                        TextEntry::make('description')
                            ->columnSpan(3),
                        TextEntry::make('type.name')
                            ->label('Type')
                            ->state(fn (ChangeRequest $record) => $record->type()->withTrashed()->first()->name)
                            ->columnSpan(3),
                        TextEntry::make('status.name')
                            ->label('Status')
                            ->state(fn (ChangeRequest $record) => $record->status()->withTrashed()->first()->name)
                            ->columnSpan(3),
                        TextEntry::make('reason')
                            ->label('Reason for change')
                            ->columnSpanFull(),
                        TextEntry::make('backout_strategy')
                            ->columnSpanFull(),
                        TextEntry::make('start_time')
                            ->dateTime()
                            ->columnSpan(2),
                        TextEntry::make('end_time')
                            ->dateTime()
                            ->columnSpan(2),
                        TextEntry::make('created_at')
                            ->label('Duration')
                            ->state(fn (ChangeRequest $record) => $record->end_time->diffForHumans($record->start_time, CarbonInterface::DIFF_ABSOLUTE, true, 6))
                            ->columnSpan(2),
                    ])
                    ->columns(6),
                Section::make('Risk Management')
                    ->schema([
                        TextEntry::make('impact')
                            ->columnSpan(1),
                        TextEntry::make('likelihood')
                            ->columnSpan(1),
                        ViewEntry::make('risk_score')
                            ->view('filament.infolists.components.change-request.risk-score')
                            ->columnSpan(1),
                    ])
                    ->columns(3),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('transition_to_approved')
                ->requiresConfirmation()
                ->modalDescription('Once this change request is approved, it can no longer be edited.')
                ->label('Transition to Approved')
                ->action(
                    fn (ChangeRequest $record) => $record->getStateMachine(SystemChangeRequestClassification::class, 'status.classification')
                        ->transitionTo(ChangeRequestStatus::tap(new ClassifiedAs(SystemChangeRequestClassification::Approved))->first(), SystemChangeRequestClassification::Approved)
                )
                ->cancelParentActions()
                ->disabled(fn (ChangeRequest $record) => ! $record->user->is(auth()->user()))
                ->visible(fn (ChangeRequest $record) => $record->doesNotNeedExplicitApproval() && $record->getStateMachine(SystemChangeRequestClassification::class, 'status.classification')->getStateTransitions()->contains(SystemChangeRequestClassification::Approved->value)),
            Action::make('transition_to_in_progress')
                ->label('Transition to In Progress')
                ->action(
                    fn (ChangeRequest $record) => $record->getStateMachine(SystemChangeRequestClassification::class, 'status.classification')
                        ->transitionTo(ChangeRequestStatus::tap(new ClassifiedAs(SystemChangeRequestClassification::InProgress))->first(), SystemChangeRequestClassification::InProgress)
                )
                ->cancelParentActions()
                ->disabled(fn (ChangeRequest $record) => ! $record->user->is(auth()->user()))
                ->visible(fn (ChangeRequest $record) => $record->getStateMachine(SystemChangeRequestClassification::class, 'status.classification')->getStateTransitions()->contains(SystemChangeRequestClassification::InProgress->value)),
            Action::make('transition_to_completed')
                ->label('Transition to Completed')
                ->action(
                    fn (ChangeRequest $record) => $record->getStateMachine(SystemChangeRequestClassification::class, 'status.classification')
                        ->transitionTo(ChangeRequestStatus::tap(new ClassifiedAs(SystemChangeRequestClassification::Completed))->first(), SystemChangeRequestClassification::Completed)
                )
                ->cancelParentActions()
                ->disabled(fn (ChangeRequest $record) => ! $record->user->is(auth()->user()))
                ->visible(fn (ChangeRequest $record) => $record->getStateMachine(SystemChangeRequestClassification::class, 'status.classification')->getStateTransitions()->contains(SystemChangeRequestClassification::Completed->value)),
            Action::make('transition_to_failed')
                ->label('Transition to Failed')
                ->action(
                    fn (ChangeRequest $record) => $record->getStateMachine(SystemChangeRequestClassification::class, 'status.classification')
                        ->transitionTo(ChangeRequestStatus::tap(new ClassifiedAs(SystemChangeRequestClassification::FailedOrReverted))->first(), SystemChangeRequestClassification::FailedOrReverted)
                )
                ->cancelParentActions()
                ->disabled(fn (ChangeRequest $record) => ! $record->user->is(auth()->user()))
                ->visible(fn (ChangeRequest $record) => $record->getStateMachine(SystemChangeRequestClassification::class, 'status.classification')->getStateTransitions()->contains(SystemChangeRequestClassification::FailedOrReverted->value)),
            EditAction::make()
                ->outlined(),
        ];
    }
}
