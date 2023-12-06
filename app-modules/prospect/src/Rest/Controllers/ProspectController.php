<?php

namespace Assist\Prospect\Rest\Controllers;

use App\Rest\Controller as RestController;
use Assist\Prospect\Rest\Resources\ProspectResource;

class ProspectController extends RestController
{
    public static $resource = ProspectResource::class;
}
