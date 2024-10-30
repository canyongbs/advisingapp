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

namespace AdvisingApp\Form\Filament\Actions;

use Illuminate\Support\Str;
use AdvisingApp\Form\Models\Form;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Query\Expression;
use Filament\Forms\Components\Wizard\Step;
use Filament\Resources\Pages\ManageRelatedRecords;
use AdvisingApp\Form\Enums\FormSubmissionRequestDeliveryMethod;

class RequestFormSubmission extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->steps([
            Step::make('Form')
                ->schema([
                    Select::make('form_id')
                        ->label('Form')
                        ->options(fn (): array => Form::query()
                            ->where('is_authenticated', true)
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all())
                        ->getSearchResultsUsing(fn (string $search): array => Form::query()
                            ->where('is_authenticated', true)
                            ->where(new Expression('lower(name)'), 'like', '%' . Str::lower($search) . '%')
                            ->limit(50)
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->helperText('Forms must have authentication enabled to be requested, to verify the identity of the respondent.'),
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

        $this->action(function (array $data, ManageRelatedRecords $livewire) {
            $submission = $livewire->getOwnerRecord()->formSubmissions()->requested()->firstOrNew(['form_id' => $data['form_id']]);
            $submission->fill($data);
            $submission->requester()->associate(auth()->user());
            $submission->save();

            $submission->deliverRequest();

            Notification::make()
                ->title('Form request sent')
                ->success()
                ->send();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'Request';
    }
}
