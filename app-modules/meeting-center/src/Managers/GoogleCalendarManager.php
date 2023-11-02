<?php

namespace Assist\MeetingCenter\Managers;

use DateTime;
use Google\Client;
use DateTimeInterface;
use Google\Service\Oauth2;
use Illuminate\Support\Carbon;
use Google\Service\Calendar\Event;
use Assist\MeetingCenter\Models\Calendar;
use Google\Service\Calendar\EventDateTime;
use Assist\MeetingCenter\Models\CalendarEvent;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\CalendarListEntry;
use Assist\MeetingCenter\Managers\Contracts\CalendarInterface;

class GoogleCalendarManager implements CalendarInterface
{
    public function getCalendars(Calendar $calendar): array
    {
        $service = (new GoogleCalendar($this->client($calendar)));

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
    public function getEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null, ?int $perPage = null): array
    {
        /**
         * @todo create without sync?
         * @todo sync uncreated events?
         * */
        $service = (new GoogleCalendar($this->client($calendar)));

        $parameters = [
            'singleEvents' => true,
            'orderBy' => 'startTime',
            'maxResults' => $perPage ?? 2500,
            'pageToken' => null,
        ];

        if (is_null($start)) {
            $start = now()->startOfDay();
        }
        $parameters['timeMin'] = $start->format(DateTimeInterface::RFC3339);

        if (is_null($end)) {
            $end = now()->addYears(2)->endOfDay();
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

    public function syncEvents(Calendar $calendar, ?Datetime $start = null, ?Datetime $end = null, ?int $perPage = null): void
    {
        $events = collect($this->getEvents($calendar, $start, $end, $perPage));

        $events
            ->each(
                function (Event $event) use ($calendar) {
                    $userEvent = $calendar->events()->where('provider_id', $event->id)->first();

                    if ($userEvent) {
                        $userEvent->fill([
                            'title' => $event->summary,
                            'description' => $event->description,
                            'starts_at' => $event->start->dateTime,
                            'ends_at' => $event->end->dateTime,
                        ]);

                        if ($userEvent->isDirty()) {
                            $userEvent->updateQuietly();
                        }
                    } else {
                        $calendar->events()
                            ->createQuietly([
                                'provider_id' => $event->id,
                                'title' => $event->summary,
                                'description' => $event->description,
                                'starts_at' => $event->start->dateTime,
                                'ends_at' => $event->end->dateTime,
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

    public static function client(?Calendar $calendar = null): Client
    {
        if ($calendar?->oauth_token) {
            $client = new Client([
                'client_id' => config('services.google_calendar.client_id'),
                'client_secret' => config('services.google_calendar.client_secret'),
                'scopes' => [
                    GoogleCalendar::CALENDAR,
                    GoogleCalendar::CALENDAR_EVENTS,
                ],
            ]);

            if ($calendar->oauth_token_expiress_at < now()) {
                $token = $client->fetchAccessTokenWithRefreshToken($calendar->oauth_refresh_token);
                $calendar->oauth_token = $token['access_token'];
                $calendar->oauth_token_expires_at = Carbon::parse($token['created'] + $token['expires_in']);
                $calendar->save();
            } else {
                $client->setAccessToken($calendar->oauth_token);
            }
        } else {
            $client = new Client([
                'client_id' => config('services.google_calendar.client_id'),
                'client_secret' => config('services.google_calendar.client_secret'),
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

        return $googleEvent;
    }
}
