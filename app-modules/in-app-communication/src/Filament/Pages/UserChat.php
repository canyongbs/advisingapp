<?php

namespace Assist\InAppCommunication\Filament\Pages;

use Filament\Pages\Page;

class UserChat extends Page
{
    public array $chats = [];

    public string $chatId = '';

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'in-app-communication::filament.pages.user-chat';

    public function generateToken()
    {
        //$token = new AccessToken(
        //    accountSid: config('services.twilio.account_sid'),
        //    signingKeySid: config('services.twilio.signing_key_sid'),
        //)
    }
}
