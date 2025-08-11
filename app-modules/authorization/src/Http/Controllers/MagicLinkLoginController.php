<?php

namespace AdvisingApp\Authorization\Http\Controllers;

use AdvisingApp\Authorization\Models\LoginMagicLink;
use Illuminate\Http\Request;

class MagicLinkLoginController
{
    public function __invoke(Request $request, LoginMagicLink $magicLink)
    {
        abort_if(
            boolean: now()->greaterThanOrEqualTo($magicLink->created_at->addMinutes(15))
                || $magicLink->used_at !== null,
            code: 403,
            message: 'Invalid link. Please request a new one.'
        );

        $user = $magicLink->user;

        abort_if(
            boolean: $user === null,
            code: 404,
            message: 'User not found.'
        );
    }
}
