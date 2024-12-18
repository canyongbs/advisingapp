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

namespace AdvisingApp\Application\Filament\Resources\ApplicationResource\Actions;

use AdvisingApp\Application\Enums\ApplicationSubmissionStateClassification;
use AdvisingApp\Application\Models\ApplicationSubmission;
use AdvisingApp\Application\Models\ApplicationSubmissionState;
use AdvisingApp\Application\Models\Scopes\ClassifiedAs;
use Filament\Tables\Actions\Action;

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
                        ->transitionTo(ApplicationSubmissionState::tap(new ClassifiedAs(ApplicationSubmissionStateClassification::Review))->first(), ApplicationSubmissionStateClassification::Review)
                )
                ->cancelParentActions()
                ->visible(fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::Review->value)),
            Action::make('mark_as_complete')
                ->label('Mark as Complete')
                ->action(
                    fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
                        ->transitionTo(ApplicationSubmissionState::tap(new ClassifiedAs(ApplicationSubmissionStateClassification::Complete))->first(), ApplicationSubmissionStateClassification::Complete)
                )
                ->cancelParentActions()
                ->visible(fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::Complete->value)),
            Action::make('mark_as_documents_required')
                ->label('Mark as Documents Required')
                ->action(
                    fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
                        ->transitionTo(ApplicationSubmissionState::tap(new ClassifiedAs(ApplicationSubmissionStateClassification::DocumentsRequired))->first(), ApplicationSubmissionStateClassification::DocumentsRequired)
                )
                ->cancelParentActions()
                ->visible(fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::DocumentsRequired->value)),
            Action::make('mark_as_deny')
                ->label('Mark as Deny')
                ->action(
                    fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
                        ->transitionTo(ApplicationSubmissionState::tap(new ClassifiedAs(ApplicationSubmissionStateClassification::Deny))->first(), ApplicationSubmissionStateClassification::Deny)
                )
                ->cancelParentActions()
                ->visible(fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::Deny->value)),
            Action::make('mark_as_admit')
                ->label('Mark as Admit')
                ->action(
                    fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')
                        ->transitionTo(ApplicationSubmissionState::tap(new ClassifiedAs(ApplicationSubmissionStateClassification::Admit))->first(), ApplicationSubmissionStateClassification::Admit)
                )
                ->cancelParentActions()
                ->visible(fn (ApplicationSubmission $record) => $record->getStateMachine(ApplicationSubmissionStateClassification::class, 'state.classification')->getStateTransitions()->contains(ApplicationSubmissionStateClassification::Admit->value)),
        ];
    }
}
