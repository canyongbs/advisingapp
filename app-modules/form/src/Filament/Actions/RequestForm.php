<?php

/*
<COPYRIGHT>

    Copyright © 2022-2023, Canyon GBS LLC. All rights reserved.

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

namespace Assist\Form\Filament\Actions;

use Assist\Form\Models\FormRequest;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Wizard\Step;
use Assist\Form\Enums\FormRequestDeliveryMethod;
use Filament\Resources\Pages\ManageRelatedRecords;

class RequestForm extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->steps([
            Step::make('Form')
                ->schema([
                    Select::make('form_id')
                        ->relationship('form', 'name', fn (Builder $query) => $query->where('is_authenticated', true))
                        ->searchable()
                        ->preload()
                        ->model(FormRequest::class)
                        ->helperText('Forms must have authentication enabled to be requested, to verify the identity of the respondent.'),
                ]),
            Step::make('Notification')
                ->schema([
                    Select::make('method')
                        ->label('How would you like to send this request?')
                        ->options(FormRequestDeliveryMethod::class)
                        ->default(FormRequestDeliveryMethod::Email->value)
                        ->selectablePlaceholder(false)
                        ->live(),
                    Textarea::make('note')
                        ->columnSpanFull(),
                ]),
        ]);

        $this->action(function (array $data, ManageRelatedRecords $livewire) {
            $livewire->getOwnerRecord()->formRequests()->create($data);

            Notification::make()
                ->title('Form request sent')
                ->success()
                ->send();
        });
    }

    public static function getDefaultName(): ?string
    {
        return 'requestFormSubmission';
    }
}
