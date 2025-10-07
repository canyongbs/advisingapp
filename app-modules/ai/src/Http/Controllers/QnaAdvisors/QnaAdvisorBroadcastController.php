<?php

namespace AdvisingApp\Ai\Http\Controllers\QnaAdvisors;

use AdvisingApp\Ai\Models\QnaAdvisor;
use Illuminate\Broadcasting\BroadcastController;
use Illuminate\Http\Request;

class QnaAdvisorBroadcastController extends BroadcastController
{
    public function auth(Request $request, QnaAdvisor $advisor)
    {
        return parent::authenticate($request);
    }
}
