<?php

return [
    'emails' => explode(',', env('DEMO_INTERNAL_USER_EMAILS') ?? ''),
];
