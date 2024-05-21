<?php

return [
    'queue' => env('MEETING_CENTER_QUEUE', env('SQS_QUEUE', 'default'))
];
