<?php

/*
<COPYRIGHT>

    Copyright © 2016-2026, Canyon GBS LLC. All rights reserved.

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

namespace AdvisingApp\MeetingCenter\Managers;

use AdvisingApp\MeetingCenter\Enums\EventTransparency;
use AdvisingApp\MeetingCenter\Exceptions\CouldNotRefreshToken;
use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Notifications\CalendarRequiresReconnectNotification;
use AdvisingApp\MeetingCenter\Settings\AzureCalendarSettings;
use Carbon\Carbon;
use DateTime;
use DateTimeInterface;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Http;
use Microsoft\Graph\Core\GraphConstants;
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model\Attendee;
use Microsoft\Graph\Model\BodyType;
use Microsoft\Graph\Model\Calendar as MicrosoftGraphCalendar;
use Microsoft\Graph\Model\DateTimeTimeZone;
use Microsoft\Graph\Model\EmailAddress;
use Microsoft\Graph\Model\Event;
use Microsoft\Graph\Model\FreeBusyStatus;
use Microsoft\Graph\Model\ItemBody;
use Symfony\Component\HttpFoundation\Response;

class OutlookCalendarManager implements CalendarInterface
{
    public function getCalendars(Calendar $calendar): array
    {
        $client = (new Graph())->setAccessToken($calendar->oauth_token);

        $calendars = $client->createRequest('GET', '/me/calendars')
            ->setReturnType(MicrosoftGraphCalendar::class)
            ->execute();

        return collect($calendars)->filter(fn (MicrosoftGraphCalendar $item) => $item->getCanEdit())
            ->mapWithKeys(fn (MicrosoftGraphCalendar $item) => [$item->getId() => $item->getName()])
            ->toArray();
    }

    public function getEvents(Calendar $calendar, ?DateTime $start = null, ?DateTime $end = null, ?int $perPage = null): array
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
                '$select' => 'id,subject,bodyPreview,start,end,attendees,showAs',
            ])
        );

        do {
            try {
                $response = $this->executeWithRetry($request);
            } catch (ClientException $exception) {
                if ($exception->getCode() === Response::HTTP_UNAUTHORIZED) {
                    $calendar = $this->refreshToken($calendar);

                    $request->setAccessToken($calendar->oauth_token);

                    $response = $this->executeWithRetry($request);
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
            $response = $this->executeWithRetry($request);
        } catch (ClientException $exception) {
            if ($exception->getCode() === Response::HTTP_UNAUTHORIZED) {
                $calendar = $this->refreshToken($event->calendar);

                $request->setAccessToken($calendar->oauth_token);

                $response = $this->executeWithRetry($request);
            } else {
                throw $exception;
            }
        }

        $event->provider_id = $response->getResponseAsObject(Event::class)->getId();
        $event->saveQuietly();
    }

    public function updateEvent(CalendarEvent $event): void
    {
        $client = $this->makeClient($event->calendar);

        $request = $client->createRequest(
            requestType: 'PATCH',
            endpoint: "/me/calendars/{$event->calendar->provider_id}/events/{$event->provider_id}",
        )
            ->attachBody($this->toMicrosoftGraphEvent($event));

        try {
            $response = $this->executeWithRetry($request);
        } catch (ClientException $exception) {
            if ($exception->getCode() === Response::HTTP_UNAUTHORIZED) {
                $calendar = $this->refreshToken($event->calendar);

                $request->setAccessToken($calendar->oauth_token);

                $response = $this->executeWithRetry($request);
            } else {
                throw $exception;
            }
        }

        $event->provider_id = $response->getResponseAsObject(Event::class)->getId();
        $event->saveQuietly();
    }

    public function deleteEvent(CalendarEvent $event): void
    {
        $client = $this->makeClient($event->calendar);

        $request = $client->createRequest(
            requestType: 'DELETE',
            endpoint: "/me/calendars/{$event->calendar->provider_id}/events/{$event->provider_id}",
        );

        try {
            $this->executeWithRetry($request);
        } catch (ClientException $exception) {
            if ($exception->getCode() === Response::HTTP_UNAUTHORIZED) {
                $calendar = $this->refreshToken($event->calendar);

                $request->setAccessToken($calendar->oauth_token);

                $this->executeWithRetry($request);
            } else {
                throw $exception;
            }
        }
    }

    public function syncEvents(Calendar $calendar, ?DateTime $start = null, ?DateTime $end = null, ?int $perPage = null): void
    {
        $start = $start ?? now()->subYear()->startOfDay();
        $end = $end ?? now()->addYear()->endOfDay();

        $providerEvents = collect($this->getEvents($calendar, $start, $end, $perPage));

        $providerEvents
            ->each(function (Event $providerEvent) use ($calendar) {
                $userEvent = $calendar->events()->where('provider_id', $providerEvent->getId())->first() ?? $calendar->events()->make();

                $userEvent->fill([
                    'provider_id' => $providerEvent->getId(),
                    'title' => filled($providerEvent->getSubject()) ? $providerEvent->getSubject() : '(No Subject)',
                    'description' => $providerEvent->getBodyPreview(),
                    'starts_at' => Carbon::parse($providerEvent->getStart()->getDateTime(), $providerEvent->getStart()->getTimeZone()),
                    'ends_at' => Carbon::parse($providerEvent->getEnd()->getDateTime(), $providerEvent->getEnd()->getTimeZone()),
                    'attendees' => collect($providerEvent->getAttendees())
                        ->map(fn ($attendee) => $attendee['emailAddress']['address'])
                        ->prepend($calendar->provider_email),
                    'transparency' => EventTransparency::fromOutlookShowAs($providerEvent->getShowAs()?->value()),
                ]);

                if ($userEvent->isDirty()) {
                    $userEvent->saveQuietly();
                }
            });

        $calendar->events()
            ->whereNull('provider_id')
            ->each(fn ($event) => $this->createEvent($event));

        // Only delete orphaned events within the synced date range
        $calendar->events()
            ->whereNotNull('provider_id')
            ->where('starts_at', '>=', $start)
            ->where('starts_at', '<', $end)
            ->whereNotIn('provider_id', $providerEvents->map(fn (Event $event) => $event->getId()))
            ->delete();
    }

    public function revokeToken(Calendar $calendar): bool
    {
        $calendar->oauth_token = null;
        $calendar->oauth_refresh_token = null;
        $calendar->save();

        return true;
    }

    public function refreshToken(Calendar $calendar): Calendar
    {
        $azureCalendarSettings = app(AzureCalendarSettings::class);

        $response = Http::asForm()->post(
            'https://login.microsoftonline.com/' . $azureCalendarSettings->tenant_id . '/oauth2/token?api-version=v1.0',
            [
                'client_id' => $azureCalendarSettings->client_id,
                'client_secret' => $azureCalendarSettings->client_secret,
                'grant_type' => 'refresh_token',
                'scope' => ['Calendars.ReadWrite', 'User.Read', 'offline_access'],
                'refresh_token' => $calendar->oauth_refresh_token,
            ]
        );

        if ($response->clientError() || $response->serverError()) {
            if ($response->status() === Response::HTTP_UNAUTHORIZED) {
                $calendar->oauth_token = null;
                $calendar->oauth_refresh_token = null;
                $calendar->oauth_token_expires_at = null;

                $calendar->save();

                $calendar->user->notify(new CalendarRequiresReconnectNotification($calendar));

                throw new CouldNotRefreshToken(previous: $response->toException());
            }

            if (
                ($response->status() === Response::HTTP_BAD_REQUEST)
                && ($response->json('error') === 'invalid_grant')
                && (
                    is_string($errorDescription = $response->json('error_description'))
                    && str_contains($errorDescription, 'AADSTS50173')
                )
            ) {
                $calendar->oauth_token = null;
                $calendar->oauth_refresh_token = null;
                $calendar->oauth_token_expires_at = null;

                $calendar->save();

                $calendar->user->notify(new CalendarRequiresReconnectNotification($calendar));

                throw new CouldNotRefreshToken(previous: $response->toException());
            }

            $response->throw();
        }

        $data = $response->object();

        $calendar->oauth_token = $data->access_token;
        $calendar->oauth_refresh_token = $data->refresh_token;
        $calendar->oauth_token_expires_at = now()->addSeconds((int) $data->expires_in);

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
        $microsoftEvent = (new Event())
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
            );

        if ($event->transparency) {
            $microsoftEvent->setShowAs(new FreeBusyStatus($event->transparency->toOutlookShowAs()));
        }

        return $microsoftEvent;
    }

    private function executeWithRetry(object $request): mixed
    {
        return retry(
            times: 3,
            callback: fn (): mixed => $request->execute(),
            sleepMilliseconds: 500,
        );
    }
}
