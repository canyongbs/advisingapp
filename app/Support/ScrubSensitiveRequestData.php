<?php

namespace App\Support;

use Sentry\Event;

class ScrubSensitiveRequestData
{
    private const SECRET_STORAGE_PATH = '/widgets/forms/store-secret';

    private const SECRET_REVEAL_PATH = '/submission-secrets/reveal';

    public static function handle(Event $event): ?Event
    {
        $request = $event->getRequest();
        $url = $request['url'] ?? null;

        if (! is_string($url)) {
            return $event;
        }

        $path = parse_url($url, PHP_URL_PATH);

        if ($path === self::SECRET_REVEAL_PATH) {
            return null;
        }

        if ($path !== self::SECRET_STORAGE_PATH) {
            return $event;
        }

        $data = $request['data'] ?? null;

        if (is_array($data)) {
            if (array_key_exists('value', $data)) {
                $data['value'] = '[Filtered]';
            }

            $request['data'] = $data;
        } elseif (! is_null($data)) {
            $request['data'] = '[Filtered]';
        }

        $event->setRequest($request);

        return $event;
    }
}
