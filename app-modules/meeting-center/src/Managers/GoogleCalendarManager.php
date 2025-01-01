<?php

/*
<COPYRIGHT>

    Copyright © 2016-2025, Canyon GBS LLC. All rights reserved.

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

use AdvisingApp\MeetingCenter\Managers\Contracts\CalendarInterface;
use AdvisingApp\MeetingCenter\Models\Calendar;
use AdvisingApp\MeetingCenter\Models\CalendarEvent;
use AdvisingApp\MeetingCenter\Notifications\CalendarRequiresReconnectNotification;
use AdvisingApp\MeetingCenter\Settings\GoogleCalendarSettings;
use DateTime;
use DateTimeInterface;
use Exception;
use Google\Client;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\CalendarListEntry;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventAttendee;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Oauth2;
use Illuminate\Support\Carbon;

class GoogleCalendarManager implements CalendarInterface
{
    /**
     * @return array<string, string>
     */
    public function getCalendars(Calendar $calendar): array
    {
        $service = (new GoogleCalendar(static::client($calendar)));

        return collect($service->calendarList->listCalendarList()
            ->getItems())
            ->filter(fn (CalendarListEntry $item) => ! str($item->id)->endsWith('@group.v.calendar.google.com'))
            ->pluck('summary', 'id')
            ->sortBy('summary')
            ->toArray();
    }

    /**
     * @see https://developers.google.com/calendar/api/v3/reference/events/watch
     *
     * @todo watch?
     * @todo multiple calendars?
     * @todo multiple users? one event? multiple events?
     * */
    public function getEvents(Calendar $calendar, ?DateTime $start = null, ?DateTime $end = null, ?int $perPage = null): array
    {
        /**
         * @todo create without sync?
         * @todo sync uncreated events?
         * */
        $service = (new GoogleCalendar(static::client($calendar)));

        $parameters = [
            'singleEvents' => true,
            'orderBy' => 'startTime',
            'maxResults' => $perPage ?? 2500,
            'pageToken' => null,
        ];

        if (is_null($start)) {
            $start = now()->subYear()->startOfDay();
        }
        $parameters['timeMin'] = $start->format(DateTimeInterface::RFC3339);

        if (is_null($end)) {
            $end = now()->addYear()->endOfDay();
        }
        $parameters['timeMax'] = $end->format(DateTimeInterface::RFC3339);

        $events = collect();

        do {
            $googleEvents = $service->events->listEvents($calendar->provider_id, $parameters);
            $parameters['pageToken'] = $googleEvents->nextPageToken;

            $events = $events->merge($service->events->listEvents($calendar->provider_id, $parameters)->getItems());
        } while ($googleEvents->nextPageToken);

        return $events->toArray();
    }

    public function createEvent(CalendarEvent $event): void
    {
        /**
         * Warning: If you add an event using the values declined, tentative, or accepted, attendees
         * with the "Add invitations to my calendar" setting set to "When I respond to invitation in email"
         * won't see an event on their calendar unless they choose to change their invitation response in
         * the event invitation email.
         *
         * @see https://developers.google.com/calendar/api/v3/reference/events/insert
         *
         * @todo check for overlapping events
         * @todo add attendees?
         * @todo auto accept?
         * @todo create later?
         * */
        $service = (new GoogleCalendar(static::client($event->calendar)));

        $googleEvent = $this->toGoogleEvent($event);
        $googleEvent = $service->events->insert($event->calendar->provider_id, $googleEvent);

        $event->updateQuietly([
            'provider_id' => $googleEvent->id,
        ]);
    }

    public function updateEvent(CalendarEvent $event): void
    {
        if ($event->provider_id) {
            $googleEvent = $this->toGoogleEvent($event);
            $service = (new GoogleCalendar(static::client($event->calendar)));
            $service->events->update($event->calendar->provider_id, $event->provider_id, $googleEvent);
        } else {
            $this->createEvent($event);
        }
    }

    public function deleteEvent(CalendarEvent $event): void
    {
        $service = (new GoogleCalendar(static::client($event->calendar)));
        $service->events->delete($event->calendar->provider_id, $event->provider_id);
    }

    public function syncEvents(Calendar $calendar, ?DateTime $start = null, ?DateTime $end = null, ?int $perPage = null): void
    {
        $events = collect($this->getEvents($calendar, $start, $end, $perPage));

        $events
            ->each(
                function (Event $event) use ($calendar) {
                    $data = [
                        'title' => $event->summary,
                        'description' => $event->description,
                        'starts_at' => $event->start->dateTime,
                        'ends_at' => $event->end->dateTime,
                        'attendees' => collect($event->getAttendees())
                            ->map(fn (EventAttendee $attendee) => $attendee->getEmail())
                            ->prepend($calendar->provider_email),
                    ];

                    $userEvent = $calendar->events()->where('provider_id', $event->id)->first();

                    if ($userEvent) {
                        $userEvent->fill($data);

                        if ($userEvent->isDirty()) {
                            $userEvent->updateQuietly();
                        }
                    } else {
                        $calendar->events()
                            ->createQuietly([
                                'provider_id' => $event->id,
                                ...$data,
                            ]);
                    }
                }
            );

        $calendar->events()
            ->whereNull('provider_id')
            ->each(fn ($event) => $this->createEvent($event));

        // TODO: needs to only delete orphaned events and not previous events
        // $calendar->events()->whereNotIn('provider_id', $events->pluck('id'))->delete();
    }

    public function revokeToken(Calendar $calendar): bool
    {
        return static::client($calendar)
            ->revokeToken($calendar->oauth_token);
    }

    public static function client(?Calendar $calendar = null): Client
    {
        if ($calendar?->oauth_token) {
            $googleCalendarSettings = app(GoogleCalendarSettings::class);

            $client = new Client([
                'client_id' => $googleCalendarSettings->client_id,
                'client_secret' => $googleCalendarSettings->client_secret,
                'scopes' => [
                    GoogleCalendar::CALENDAR,
                    GoogleCalendar::CALENDAR_EVENTS,
                ],
            ]);

            if ($calendar->oauth_token_expires_at < now()) {
                $calendar = (new self())->refreshToken($calendar);
            }

            $client->setAccessToken($calendar->oauth_token);
        } else {
            $googleCalendarSettings = app(GoogleCalendarSettings::class);

            $client = new Client([
                'client_id' => $googleCalendarSettings->client_id,
                'client_secret' => $googleCalendarSettings->client_secret,
                'scopes' => [
                    Oauth2::OPENID,
                    Oauth2::USERINFO_EMAIL,
                    Oauth2::USERINFO_PROFILE,
                    GoogleCalendar::CALENDAR,
                    GoogleCalendar::CALENDAR_EVENTS,
                ],
                'redirect_uri' => route('calendar.google.callback'),
                'prompt' => 'consent',
                'access_type' => 'offline',
            ]);
        }

        return $client;
    }

    public function refreshToken(Calendar $calendar): Calendar
    {
        try {
            $googleCalendarSettings = app(GoogleCalendarSettings::class);

            $client = new Client([
                'client_id' => $googleCalendarSettings->client_id,
                'client_secret' => $googleCalendarSettings->client_secret,
                'scopes' => [
                    GoogleCalendar::CALENDAR,
                    GoogleCalendar::CALENDAR_EVENTS,
                ],
            ]);

            $token = $client->fetchAccessTokenWithRefreshToken($calendar->oauth_refresh_token);

            if (empty($token['access_token']) || empty($token['expires_in']) || empty($token['created']) || empty($token['refresh_token'])) {
                throw new Exception('fetchAccessTokenWithRefreshToken did not return a valid token');
            }

            $calendar->oauth_token = $token['access_token'];
            $calendar->oauth_token_expires_at = Carbon::parse($token['created'] + $token['expires_in']);
            $calendar->oauth_refresh_token = $token['refresh_token'];
            $calendar->save();
        } catch (Exception $e) {
            $calendar->update([
                'oauth_token' => null,
                'oauth_refresh_token' => null,
                'oauth_token_expires_at' => null,
            ]);

            $calendar->user->notify(new CalendarRequiresReconnectNotification($calendar));

            throw $e;
        }

        return $calendar;
    }

    private function toGoogleEvent(CalendarEvent $event): Event
    {
        $googleEvent = new Event();
        $googleEvent->setSummary($event->title);
        $googleEvent->setDescription($event->description);

        $start = new EventDateTime();
        $start->setDateTime($event->starts_at);
        $googleEvent->setStart($start);

        $end = new EventDateTime();
        $end->setDateTime($event->ends_at);
        $googleEvent->setEnd($end);

        $attendees = collect($event->attendees)
            // If you add yourself as an attendee you end up with a weird duplicate event on the calendar...
            ->reject(fn (string $email): bool => $email === $event->calendar->provider_email)
            ->map(function ($email) {
                $attendee = new EventAttendee();
                $attendee->setEmail($email);

                return $attendee;
            })
            ->flatten()
            ->toArray();

        $googleEvent->setAttendees($attendees);

        return $googleEvent;
    }
}
