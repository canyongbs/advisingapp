<?php

namespace App\JsonApi\V1;

use Assist\Prospect\JsonApi\V1\Prospects\ProspectStatusSchema;
use LaravelJsonApi\Core\Server\Server as BaseServer;
use Assist\Prospect\JsonApi\V1\Prospects\ProspectSchema;

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
        ];
    }
}
