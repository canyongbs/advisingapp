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

namespace Assist\MeetingCenter\Managers;

use DateTime;
use Carbon\Carbon;
use DateTimeInterface;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\Event;
use Microsoft\Graph\Model\Attendee;
use Microsoft\Graph\Model\BodyType;
use Microsoft\Graph\Model\ItemBody;
use Illuminate\Support\Facades\Http;
use Microsoft\Graph\Model\EmailAddress;
use Microsoft\Graph\Core\GraphConstants;
use Assist\MeetingCenter\Models\Calendar;
use GuzzleHttp\Exception\ClientException;
use Microsoft\Graph\Model\DateTimeTimeZone;
use Assist\MeetingCenter\Models\CalendarEvent;
use Assist\MeetingCenter\Managers\Contracts\CalendarInterface;

class OutlookCalendarManager implements CalendarInterface
{
    /**
     * @return array<string, string>
     */
    public function getCalendars(Calendar $calendar): array
    {
        $client = (new Graph())->setAccessToken($calendar->oauth_token);

        $calendars = $client->createRequest('GET', '/me/calendars')
            ->setReturnType(\Microsoft\Graph\Model\Calendar::class)
            ->execute();

        return collect($calendars)->filter(fn (\Microsoft\Graph\Model\Calendar $item) => $item->getCanEdit())
            ->mapWithKeys(fn (\Microsoft\Graph\Model\Calendar $item) => [$item->getId() => $item->getName()])
            ->toArray();
    }

    // https://github.com/microsoftgraph/msgraph-sample-phpapp/tree/main
    // https://github.com/microsoftgraph/msgraph-sdk-php
    // https://learn.microsoft.com/en-us/graph/api/resources/webhooks?view=graph-rest-1.0

    public function getEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null, ?int $perPage = null): array
    {
        $client = $this->makeClient($calendar);

        $start = $start ?? now()->subYear()->startOfDay();

        $end = $end ?? now()->addYear()->endOfDay();

        $events = [];

        $request = $client->createCollectionRequest(
            requestType: 'GET',
            endpoint: '/me/calendar/calendarView?' . http_build_query([
                '$top' => $perPage ?? GraphConstants::MAX_PAGE_SIZE,
                'startDateTime' => $start->format(DateTimeInterface::ATOM),
                'endDateTime' => $end->format(DateTimeInterface::ATOM),
            ])
        );

        do {
            try {
                $response = $request->execute();
            } catch (ClientException $exception) {
                if ($exception->getCode() === 401) {
                    $calendar = $this->refreshToken($calendar);

                    $request->setAccessToken($calendar->oauth_token);

                    $response = $request->execute();
                } else {
                    throw $exception;
                }
            }

            $events = array_merge($events, $response->getResponseAsObject(Event::class));

            if ($response->getNextLink() !== null) {
                $request = $client->createCollectionRequest(
                    requestType: 'GET',
                    endpoint: $response->getNextLink()
                );
            } else {
                $request = null;
            }
        } while ($request !== null);

        return $events;
    }

    public function createEvent(CalendarEvent $event): void
    {
        $client = $this->makeClient($event->calendar);

        $request = $client->createRequest(
            requestType: 'POST',
            endpoint: "/me/calendars/{$event->calendar->provider_id}/events",
        )
            ->attachBody($this->toMicrosoftGraphEvent($event));

        try {
            $response = $request->execute();
        } catch (ClientException $exception) {
            if ($exception->getCode() === 401) {
                $calendar = $this->refreshToken($event->calendar);

                $request->setAccessToken($calendar->oauth_token);

                $response = $request->execute();
            } else {
                throw $exception;
            }
        }

        $event->provider_id = $response->getResponseAsObject(Event::class)->getId();
        $event->saveQuietly();
    }

    public function updateEvent(CalendarEvent $event): void
    {
        // https://learn.microsoft.com/en-us/graph/api/event-update?view=graph-rest-1.0&tabs=http

        // TODO: Implement updateEvent() method.
    }

    public function deleteEvent(CalendarEvent $event): void
    {
        // https://learn.microsoft.com/en-us/graph/api/event-delete?view=graph-rest-1.0&tabs=http

        // TODO: Implement deleteEvent() method.
    }

    public function syncEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null, ?int $perPage = null): void
    {
        collect($this->getEvents($calendar, $start, $end, $perPage))
            ->each(function (Event $providerEvent) use ($calendar) {
                $userEvent = $calendar->events()->where('provider_id', $providerEvent->getId())->first() ?? $calendar->events()->make();

                $userEvent->fill([
                    'provider_id' => $providerEvent->getId(),
                    'title' => $providerEvent->getSubject(),
                    'description' => $providerEvent->getBodyPreview(),
                    'starts_at' => Carbon::parse($providerEvent->getStart()->getDateTime(), $providerEvent->getStart()->getTimeZone()),
                    'ends_at' => Carbon::parse($providerEvent->getEnd()->getDateTime(), $providerEvent->getEnd()->getTimeZone()),
                    'attendees' => collect($providerEvent->getAttendees())
                        ->map(fn ($attendee) => $attendee['emailAddress']['address'])
                        ->prepend($calendar->provider_email),
                ]);

                if ($userEvent->isDirty()) {
                    $userEvent->saveQuietly();
                }
            });

        // TODO: Create events that don't exist in the provider calendar

        // TODO: Delete events that don't exist in the provider calendar anymore
    }

    public function revokeToken(Calendar $calendar): bool
    {
        // TODO: Implement revokeToken() method.
        return false;
    }

    public function refreshToken(Calendar $calendar): Calendar
    {
        $response = Http::asForm()->post(
            'https://login.microsoftonline.com/' . config('services.azure_calendar.tenant_id') . '/oauth2/token?api-version=v1.0',
            [
                'client_id' => config('services.azure_calendar.client_id'),
                'client_secret' => config('services.azure_calendar.client_secret'),
                'grant_type' => 'refresh_token',
                'scope' => ['Calendars.ReadWrite', 'User.Read', 'offline_access'],
                'refresh_token' => $calendar->oauth_refresh_token,
            ]
        );

        if ($response->clientError() || $response->serverError()) {
            if ($response->clientError()) {
                // TODO: Send out an alert to the user that their token has expired and they need to re-authenticate
            }

            $response->throw();
        }

        $data = $response->object();

        $calendar->oauth_token = $data->access_token;
        $calendar->oauth_refresh_token = $data->refresh_token;
        $calendar->oauth_token_expires_at = now()->addSeconds($data->expires_in);

        $calendar->save();

        return $calendar;
    }

    public function makeClient(Calendar $calendar): Graph
    {
        if ($calendar->oauth_token_expires_at->isPast()) {
            $calendar = $this->refreshToken($calendar);
        }

        return (new Graph())->setAccessToken($calendar->oauth_token);
    }

    protected function toMicrosoftGraphEvent(CalendarEvent $event): Event
    {
        return (new Event())
            ->setSubject($event->title)
            ->setBody(
                (new ItemBody())
                    ->setContentType(new BodyType(BodyType::HTML))
                    ->setContent($event->description)
            )
            ->setStart(
                (new DateTimeTimeZone())
                    ->setDateTime((new DateTime($event->starts_at))->format(DateTimeInterface::ATOM))
                    // TODO: Fix timezone to work with system changes to working with timezone once we get to it
                    ->setTimeZone('UTC')
            )
            ->setEnd(
                (new DateTimeTimeZone())
                    ->setDateTime((new DateTime($event->ends_at))->format(DateTimeInterface::ATOM))
                    // TODO: Fix timezone to work with system changes to working with timezone once we get to it
                    ->setTimeZone('UTC')
            )
            ->setAttendees(
                collect($event->attendees)
                    ->reject(fn ($attendee) => $attendee === $event->calendar->provider_email)
                    ->map(
                        fn ($attendee) => (new Attendee())
                            ->setEmailAddress(
                                (new EmailAddress())
                                    ->setAddress($attendee)
                            )
                    )
                    ->flatten()
                    ->toArray()
            )
            ->setTransactionId($event->id);
    }
}
