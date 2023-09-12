<?php

namespace Assist\Assistant\Services\AIInterface\Contracts;

interface AIInterface
{
    public function ask(string $message): string;
}
