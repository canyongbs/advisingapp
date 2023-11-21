<?php

/*
<COPYRIGHT>

Copyright © 2022-2023, Canyon GBS LLC

All rights reserved.

This file is part of a project developed using Laravel, which is an open-source framework for PHP.
Canyon GBS LLC acknowledges and respects the copyright of Laravel and other open-source
projects used in the development of this solution.

This project is licensed under the Affero General Public License (AGPL) 3.0.
For more details, see https://github.com/canyongbs/assistbycanyongbs/blob/main/LICENSE.

Notice:
- The copyright notice in this file and across all files and applications in this
 repository cannot be removed or altered without violating the terms of the AGPL 3.0 License.
- The software solution, including services, infrastructure, and code, is offered as a
 Software as a Service (SaaS) by Canyon GBS LLC.
- Use of this software implies agreement to the license terms and conditions as stated
 in the AGPL 3.0 License.

For more information or inquiries please visit our website at
https://www.canyongbs.com or contact us via email at legal@canyongbs.com.

</COPYRIGHT>
*/

namespace Assist\Engagement\Filament\Actions;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard\Step;
use Assist\Engagement\Models\EngagementDeliverable;
use Assist\Engagement\Enums\EngagementDeliveryMethod;
use Assist\Engagement\Actions\CreateDeliverablesForEngagement;

class EngagementCreateAction
{
    public static function make(Model $educatable)
    {
        return CreateAction::make('engage')
            ->record($educatable)
            ->icon('heroicon-o-chat-bubble-bottom-center-text')
            ->modalHeading('Send Engagement')
            ->modalDescription("Send an engagement to {$educatable->display_name}.")
            ->steps([
                Step::make('Choose your delivery methods')
                    ->description('Select email, sms, or both.')
                    ->schema([
                        Select::make('delivery_methods')
                            ->label('How would you like to send this engagement?')
                            ->translateLabel()
                            ->options(EngagementDeliveryMethod::class)
                            ->multiple()
                            ->minItems(1)
                            ->validationAttribute('Delivery Method')
                            ->required(),
                    ]),
                Step::make('Engagement Details')
                    ->description('Add the details of the engagement.')
                    ->schema([
                        TextInput::make('subject')
                            ->autofocus()
                            ->translateLabel()
                            ->required()
                            ->placeholder(__('Subject'))
                            ->hidden(fn (callable $get) => collect($get('delivery_methods'))->doesntContain(EngagementDeliveryMethod::Email->value))
                            ->helperText('The subject will only be used for the email delivery method.'),
                        Textarea::make('body')
                            ->translateLabel()
                            ->placeholder(__('Body'))
                            ->required()
                            ->maxLength(function (callable $get) {
                                if (collect($get('delivery_methods'))->contains(EngagementDeliveryMethod::Sms->value)) {
                                    return 320;
                                }

                                return 65535;
                            })
                            ->helperText(function (callable $get) {
                                if (collect($get('delivery_methods'))->contains(EngagementDeliveryMethod::Sms->value)) {
                                    return 'The body of your message can be up to 320 characters long.';
                                }

                                return 'The body of your message can be up to 65,535 characters long.';
                            }),
                    ]),
            ])
            ->action(function (array $data) use ($educatable) {
                // TODO Probably extract all of this to an action
                $engagement = $educatable->engagements()->create([
                    'subject' => $data['subject'],
                    'body' => $data['body'],
                ]);

                $createDeliverablesForEngagement = resolve(CreateDeliverablesForEngagement::class);

                $createDeliverablesForEngagement($engagement, $data['delivery_methods']);

                $engagement->deliverables()->each(function (EngagementDeliverable $deliverable) {
                    $deliverable->deliver();
                });
            })
            ->modalSubmitActionLabel('Send')
            ->modalCloseButton(false)
            ->closeModalByClickingAway(false)
            // FIXME This is currently not working exactly as expected. Dan is taking a look and will report back
            ->modalCancelAction(
                fn ($action) => Action::make('cancel')
                    ->requiresConfirmation()
                    ->modalDescription(fn () => 'The message has not been sent, are you sure you wish to cancel?')
                    ->cancelParentActions()
                    ->close()
                    ->color('gray'),
            );
    }
}
