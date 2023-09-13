<?php

namespace Assist\Assistant\Services\AIInterface\Contracts;

use Assist\Assistant\Services\AIInterface\DataTransferObjects\Chat;

interface AIInterface
{
    public function ask(Chat $chat): string;
}
