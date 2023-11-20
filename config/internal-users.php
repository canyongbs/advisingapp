<?php

return [
    'emails' => env('DEMO_INTERNAL_USER_EMAILS') ? explode(',', env('DEMO_INTERNAL_USER_EMAILS')) : null,
];
