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

namespace AdvisingApp\IntegrationAwsSesEventHandling\Jobs;

use AdvisingApp\IntegrationAwsSesEventHandling\Actions\DispatchSesEvent;
use AdvisingApp\IntegrationAwsSesEventHandling\DataTransferObjects\SesEventData;
use AdvisingApp\IntegrationAwsSesEventHandling\Exceptions\CouldNotFindTenantFromData;
use Aws\Sns\Message;
use Aws\Sns\MessageValidator;
use Aws\Sqs\SqsClient;
use Exception;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\Http;
use Spatie\Multitenancy\Jobs\NotTenantAware;
use Throwable;

/**
 * Drains the SES events queue so a mass send's delivery events reach the workers through SQS instead of flooding the web service.
 * Each run polls for a bounded time and leaves any remaining backlog to the next one.
 */
class ConsumeSesEventsFromSqs implements ShouldQueue, ShouldBeUnique, NotTenantAware
{
    use Queueable;

    private const int RUN_BUDGET_SECONDS = 50;

    private const int MAX_MESSAGES_PER_RECEIVE = 10;

    private const int WAIT_TIME_SECONDS = 20;

    public int $uniqueFor = 300;

    public function __construct()
    {
        $this->onQueue(config('queue.landlord_queue'));
    }

    /**
     * @return array<int, object>
     */
    public function middleware(): array
    {
        return [(new WithoutOverlapping())->dontRelease()->expireAfter(self::RUN_BUDGET_SECONDS + 60)];
    }

    public function handle(DispatchSesEvent $dispatchSesEvent, MessageValidator $validator, SqsClient $client): void
    {
        $queueUrl = config('services.ses_events_queue.url');

        if (blank($queueUrl)) {
            return;
        }

        $deadline = now()->addSeconds(self::RUN_BUDGET_SECONDS);

        while (now()->lessThan($deadline)) {
            $messages = $client->receiveMessage([
                'QueueUrl' => $queueUrl,
                'MaxNumberOfMessages' => self::MAX_MESSAGES_PER_RECEIVE,
                'WaitTimeSeconds' => self::WAIT_TIME_SECONDS,
            ])->get('Messages');

            if (! is_array($messages) || $messages === []) {
                return;
            }

            $handledMessages = [];

            foreach ($messages as $message) {
                assert(is_array($message));

                if ($this->handleMessage($message, $dispatchSesEvent, $validator)) {
                    $handledMessages[] = [
                        'Id' => $message['MessageId'],
                        'ReceiptHandle' => $message['ReceiptHandle'],
                    ];
                }
            }

            if ($handledMessages !== []) {
                $client->deleteMessageBatch([
                    'QueueUrl' => $queueUrl,
                    'Entries' => $handledMessages,
                ]);
            }
        }
    }

    /**
     * @param array<array-key, mixed> $message
     *
     * @return bool Whether the message can be deleted. Otherwise SQS redelivers it until it moves to the dead-letter queue.
     */
    protected function handleMessage(array $message, DispatchSesEvent $dispatchSesEvent, MessageValidator $validator): bool
    {
        try {
            $envelope = json_decode(is_string($message['Body'] ?? null) ? $message['Body'] : '', true);

            if (! is_array($envelope)) {
                report(new Exception('An SES events SQS message body is not valid JSON.'));

                return false;
            }

            // The queue policy only accepts the SES topic; validating the signature as well guards against anything else getting in.
            if (! $validator->isValid(new Message($envelope))) {
                report(new Exception('An SES events SQS message failed SNS signature validation.'));

                return false;
            }

            $type = $envelope['Type'] ?? null;

            if ($type === 'SubscriptionConfirmation') {
                return $this->confirmSubscription($envelope);
            }

            if ($type === 'UnsubscribeConfirmation') {
                return true;
            }

            if ($type !== 'Notification') {
                report(new Exception('An unexpected SNS message type arrived on the SES events queue.'));

                return true;
            }

            $snsMessageId = $envelope['MessageId'] ?? null;

            $dispatchSesEvent(SesEventData::createFromSnsEnvelope($envelope), is_string($snsMessageId) ? $snsMessageId : null);

            return true;
        } catch (CouldNotFindTenantFromData $exception) {
            report($exception);

            // It can never be routed, so retrying would only fill the dead-letter queue.
            return true;
        } catch (Throwable $exception) {
            report($exception);

            return false;
        }
    }

    /**
     * @param array<array-key, mixed> $envelope
     */
    protected function confirmSubscription(array $envelope): bool
    {
        $subscribeUrl = $envelope['SubscribeURL'] ?? null;

        if (! is_string($subscribeUrl)) {
            return true;
        }

        return Http::timeout(10)->get($subscribeUrl)->successful();
    }
}
