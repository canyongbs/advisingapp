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

namespace AdvisingApp\Application\Filament\Actions;

use AdvisingApp\Application\Models\Application;
use AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Wizard\Step;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Str;

class RequestApplicationSubmission extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->steps([
            Step::make('Application')
                ->schema([
                    Select::make('application_id')
                        ->label('Application')
                        ->required()
                        ->options(fn (): array => Application::query()
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all())
                        ->getSearchResultsUsing(fn (string $search): array => Application::query()
                            ->where(new Expression('lower(name)'), 'like', '%' . Str::lower($search) . '%')
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all())
                        ->getOptionLabelUsing(fn (string | int | null $value): ?string => filled($value)
                            ? Application::query()
                                ->whereKey($value)
                                ->value('name')
                            : null)
                        ->searchable(),
                ]),
            Step::make('Notification')
                ->schema([
                    Select::make('request_method')
                        ->label('How would you like to send this request?')
                        ->options(FormSubmissionRequestDeliveryMethod::class)
                        ->default(FormSubmissionRequestDeliveryMethod::Email->value)
                        ->selectablePlaceholder(false)
                        ->live(),
                    Textarea::make('request_note')
                        ->columnSpanFull(),
                ]),
        ]);

        $this->action(function (array $data, ManageRelatedRecords | RelationManager $livewire) {
            $submission = $livewire->getOwnerRecord()->applicationSubmissions()->requested()->firstOrNew(['application_id' => $data['application_id']]);
            $submission->fill($data);
            $submission->requester()->associate(auth()->user());
            $submission->save();

            $submission->deliverRequest();

            Notification::make()
                ->title('Application request sent')
                ->success()
                ->send();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'Request';
    }
}
