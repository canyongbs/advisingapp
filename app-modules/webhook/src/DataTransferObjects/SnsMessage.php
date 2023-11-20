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

namespace Assist\Webhook\DataTransferObjects;

use Illuminate\Http\Request;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class SnsMessage extends Data
{
    public function __construct(
        public string $type,
        public string $messageId,
        public string $topicArn,
        public string|Optional $subject,
        public string $message,
        public string $timestamp,
        public string $signatureVersion,
        public string $signature,
        public string $signingCertURL,
        public string|Optional $subscribeURL,
        public string|Optional $unsubscribeURL,
    ) {}

    public static function fromRequest(Request $request): static
    {
        $data = json_decode($request->getContent(), true);

        return new self(
            type: $data['Type'],
            messageId: $data['MessageId'],
            topicArn: $data['TopicArn'],
            subject: $data['Subject'] ?? Optional::create(),
            message: $data['Message'],
            timestamp: $data['Timestamp'],
            signatureVersion: $data['SignatureVersion'],
            signature: $data['Signature'],
            signingCertURL: $data['SigningCertURL'],
            subscribeURL: $data['SubscribeURL'] ?? Optional::create(),
            unsubscribeURL: $data['UnsubscribeURL'] ?? Optional::create(),
        );
    }
}
