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

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Event;
use Illuminate\Mail\Events\MessageSent;

it('sets the X-SES-CONFIGURATION-SET header if mail.mailers.ses.configuration_set is set', function () {
    Event::fake(MessageSent::class);

    $configurationSet = 'test';

    config(['mail.mailers.ses.configuration_set' => $configurationSet]);

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

    config(['mail.mailers.ses.configuration_set' => null]);

    Mail::raw(
        'Hello, welcome to Laravel!',
        fn ($message) => $message->to('test@test.com')->subject('Test')
    );

    Event::assertDispatched(
        fn (MessageSent $event) => is_null($event->message->getHeaders()->get('X-SES-CONFIGURATION-SET'))
    );
});
