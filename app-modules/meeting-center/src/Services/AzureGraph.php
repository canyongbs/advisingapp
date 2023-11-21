<?php

namespace Assist\MeetingCenter\Services;

use Microsoft\Graph\Graph;

class AzureGraph extends Graph
{
    public string $accessToken;

    public string $refreshToken;

    public function setAccessToken($accessToken): self
    {
        $this->accessToken = $accessToken;

        return parent::setAccessToken($accessToken);
    }

    public function setRefreshToken(string $refreshToken): self
    {
        $this->refreshToken = $refreshToken;

        return $this;
    }
}
