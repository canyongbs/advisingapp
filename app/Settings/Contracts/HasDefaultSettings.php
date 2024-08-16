<?php

namespace App\Settings\Contracts;

interface HasDefaultSettings
{
    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array;
}
