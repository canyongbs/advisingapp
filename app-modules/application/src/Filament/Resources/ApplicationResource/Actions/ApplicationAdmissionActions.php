<?php

namespace AdvisingApp\Application\Filament\Resources\ApplicationResource\Actions;

use Filament\Tables\Actions\Action;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;

// TODO We either need to introduce support for choosing the state that abides by the allowed classification transitions
// Or we need to restrict creation of these states so that our `first()` logic to grab the desired state is always correct
class ApplicationAdmissionActions
{
    public static function get(): array
    {
        return [
            Action::make('mark_as_reviewed')
                ->label('Mark as Reviewed')
                ->action(
                    fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
                        ->transitionTo(ApplicationSubmissionState::review()->first(), ApplicationSubmissionStateClassification::Review)
                )
                ->cancelParentActions()
                ->visible(function (ApplicationSubmission $record) {
                    return $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::Review->value);
                }),
            Action::make('mark_as_complete')
                ->label('Mark as Complete')
                ->action(
                    fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
                        ->transitionTo(ApplicationSubmissionState::complete()->first(), ApplicationSubmissionStateClassification::Complete)
                )
                ->cancelParentActions()
                ->visible(function (ApplicationSubmission $record) {
                    return $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::Complete->value);
                }),
            Action::make('mark_as_documents_required')
                ->label('Mark as Documents Required')
                ->action(
                    fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
                        ->transitionTo(ApplicationSubmissionState::documentsRequired()->first(), ApplicationSubmissionStateClassification::DocumentsRequired)
                )
                ->cancelParentActions()
                ->visible(function (ApplicationSubmission $record) {
                    return $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::DocumentsRequired->value);
                }),
            Action::make('mark_as_deny')
                ->label('Mark as Deny')
                ->action(
                    fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
                        ->transitionTo(ApplicationSubmissionState::deny()->first(), ApplicationSubmissionStateClassification::Deny)
                )
                ->cancelParentActions()
                ->visible(function (ApplicationSubmission $record) {
                    return $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::Deny->value);
                }),
            Action::make('mark_as_admit')
                ->label('Mark as Admit')
                ->action(
                    fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
                        ->transitionTo(ApplicationSubmissionState::admit()->first(), ApplicationSubmissionStateClassification::Admit)
                )
                ->cancelParentActions()
                ->visible(function (ApplicationSubmission $record) {
                    return $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::Admit->value);
                }),
        ];
    }
}
