<?php

namespace App\JsonApi\V1;

use Assist\Prospect\JsonApi\V1\Prospects\ProspectSchema;
use Assist\Prospect\JsonApi\V1\ProspectSources\ProspectSourceSchema;
use Assist\Prospect\JsonApi\V1\ProspectStatuses\ProspectStatusSchema;
use LaravelJsonApi\Core\Server\Server as BaseServer;

class Server extends BaseServer
{
    /**
     * The base URI namespace for this server.
     *
     * @var string
     */
    protected string $baseUri = '/api/v1';

    /**
     * Bootstrap the server when it is handling an HTTP request.
     *
     * @return void
     */
    public function serving(): void
    {
        // no-op
    }

    public function authorizable(): bool
    {
        //TODO: use real auth
        return false;
    }

    /**
     * Get the server's list of schemas.
     *
     * @return array
     */
    protected function allSchemas(): array
    {
        return [
            ProspectSchema::class,
            ProspectStatusSchema::class,
            ProspectSourceSchema::class,
        ];
    }
}
