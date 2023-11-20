<?php

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
