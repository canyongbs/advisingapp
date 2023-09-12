<?php

namespace Assist\Assistant\Services\AIInterface;

use Assist\Assistant\Services\AIInterface\Contracts\AIInterface;

class AzureOpenAI implements AIInterface
{
    public function ask(string $message): string
    {
        sleep(4);

        return 'This is a response from Azure Open AI.';
    }
}
