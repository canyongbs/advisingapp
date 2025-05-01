<?php

namespace AdvisingApp\Notification\Enums;

use AdvisingApp\Notification\Models\EmailMessage;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Support\Facades\Log;

enum EmailMessageDisplayStatus
{
  case Complaint;
  case Bounced;
  case Failed;
  case Clicked;
  case Read;
  case Delivered;
  case Delayed;
  case Unsubscribed;
  case Sent;

    public function getLabel(): ?string
    {
        return str($this->name)->headline();
    }

    public static function getStatusFromEmailMessage($message) :string {
        if (!$message) {
            return '';
        }

        return match (true) {
            $message->events()->where('type', EmailMessageEventType::Complaint->value)->exists() => 'Complaint',
            $message->events()->where('type', EmailMessageEventType::Bounce->value)->exists() => 'Bounced',
            $message->events()->where('type', EmailMessageEventType::Reject->value)->orWhere('type', EmailMessageEventType::RenderingFailure->value)->exists() => 'Failed',
            $message->events()->where('type', EmailMessageEventType::Click->value)->exists() => 'Clicked',
            $message->events()->where('type', EmailMessageEventType::Open->value)->exists() => 'Read',
            $message->events()->where('type', EmailMessageEventType::Delivery->value)->exists() => 'Delivered',
            $message->events()->where('type', EmailMessageEventType::DeliveryDelay->value)->exists() => 'Delayed',
            $message->events()->where('type', EmailMessageEventType::Subscription->value)->exists() => 'Unsubscribed',
            $message->events()->where('type', EmailMessageEventType::Send->value)->exists() => 'Sent',
            default => '',
        };
    }
    public function color(): string
    {
        return match ($this) {
            self::Delivered, self::Read, self::Clicked => 'success',
            self::Delayed => 'primary',
            self::Failed, self::Bounced, self::Complaint => 'danger',
            self::Sent, self::Unsubscribed => 'gray',
            default => 'gray',
        };
    }
}
