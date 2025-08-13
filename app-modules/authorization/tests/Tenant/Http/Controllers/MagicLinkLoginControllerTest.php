<?php

use AdvisingApp\Authorization\Models\LoginMagicLink;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

use function Pest\Laravel\get;

it('requires a valid signed URL ', function () {
    $code = Str::random();

    $magicLink = LoginMagicLink::factory()->withCode($code)->create();

    get(route('magic-link.login', [
        'magicLink' => $magicLink->id,
        'payload' => urlencode(Crypt::encrypt([
            'code' => $code,
            'user_id' => $magicLink->user_id,
        ])),
    ]))
        ->assertForbidden();
});

//it('rejects a magic link that is older than 15 minutes', function () {});
//
//it('rejects a magic link that has already been used', function () {});
//
//it('rejects a magic link with an invalid code', function () {});
//
//it('rejects a magic link with a non-matching user ID', function () {});
//
//it('logs in the user and redirects to the admin panel home', function () {});
