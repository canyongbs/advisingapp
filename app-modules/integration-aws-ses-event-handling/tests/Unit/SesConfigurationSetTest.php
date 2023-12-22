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

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;
use AdvisingApp\IntegrationAwsSesEventHandling\Settings\SesSettings;

it('sets the X-SES-CONFIGURATION-SET header if mail.mailers.ses.configuration_set is set', function () {
    Event::fake(MessageSent::class);

    $configurationSet = 'test';

    $settings = app(SesSettings::class);
    $settings->configuration_set = $configurationSet;
    $settings->save();

    Mail::raw(
        'Hello, welcome to Laravel!',
        fn ($message) => $message->to('test@test.com')->subject('Test')
    );

    Event::assertDispatched(
        fn (MessageSent $event) => $event->message->getHeaders()->get('X-SES-CONFIGURATION-SET')->getBody() === $configurationSet
    );
});

it('X-SES-CONFIGURATION-SET is not present if mail.mailers.ses.configuration_set is not', function () {
    Event::fake(MessageSent::class);

    Mail::raw(
        'Hello, welcome to Laravel!',
        fn ($message) => $message->to('test@test.com')->subject('Test')
    );

    Event::assertDispatched(
        fn (MessageSent $event) => is_null($event->message->getHeaders()->get('X-SES-CONFIGURATION-SET'))
    );
});
