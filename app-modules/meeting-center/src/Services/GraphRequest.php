<?php

namespace Assist\MeetingCenter\Services;

class GraphRequest extends \Microsoft\Graph\Http\GraphRequest
{
    public function execute($client = null)
    {
        return parent::execute($client);
    }
}
