<?php

namespace Assist\Assistant\Services\AIInterface;

use Assist\Assistant\Services\AIInterface\Contracts\AIInterface;
use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;

class AzureOpenAI implements AIInterface
{
    public function ask(Chat $chat): string
    {
        // TODO: Actual implementation, prepare the data from the chat messages adding system context and then appending messages
        sleep(3);

        return 'This is a response from Azure Open AI.';
    }
}
