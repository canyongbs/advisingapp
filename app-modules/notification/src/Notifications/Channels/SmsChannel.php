<?php

namespace AdvisingApp\Notification\Notifications\Channels;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use AdvisingApp\Notification\Messages\TwilioMessage;
use AdvisingApp\Notification\Notifications\SmsNotification;
use AdvisingApp\Notification\Notifications\BaseNotification;
use AdvisingApp\Notification\DataTransferObjects\SmsChannelResultData;
use AdvisingApp\Notification\DataTransferObjects\NotificationResultData;

class SmsChannel
{
    public function send(object $notifiable, BaseNotification $notification): void
    {
        $deliverable = $notification->beforeSend($notifiable, SmsChannel::class);

        if ($deliverable === false) {
            // Do anything else we need to to notify sending party that notification was not sent
            return;
        }

        $smsData = $this->handle($notifiable, $notification);

        $notification->afterSend($notifiable, $deliverable, $smsData);
    }

    public function handle(object $notifiable, BaseNotification $notification): NotificationResultData
    {
        // TODO Figure out if we can remove this easily/nicely...
        /** @var SmsNotification $notification */
        /** @var TwilioMessage $twilioMessage */
        $twilioMessage = $notification->toSms($notifiable);

        $client = new Client(config('services.twilio.account_sid'), config('services.twilio.auth_token'));

        $messageContent = [
            'from' => $twilioMessage->getFrom(),
            'body' => $twilioMessage->getContent(),
        ];

        if (! app()->environment('local')) {
            $messageContent['statusCallback'] = route('inbound.webhook.twilio', ['event' => 'status_callback']);
        }

        // TODO Clean this up
        $smsResult = SmsChannelResultData::from([
            'success' => false,
        ]);

        $result = NotificationResultData::from([
            'type' => $smsResult,
        ]);

        try {
            $message = $client->messages->create(
                ! is_null(config('services.twilio.test_to_number')) ? config('services.twilio.test_to_number') : $twilioMessage->getRecipientPhoneNumber(),
                $messageContent
            );

            $result->type->success = true;
            $result->type->message = $message;
        } catch (TwilioException $e) {
            $result->type->error = $e->getMessage();
        }

        return $result;
    }
}
