<?php

namespace App\Filament\Widgets\Concerns;

trait FormatsCount
{
    protected function formatCount(int $count): string
    {
        if ($count < 10000) {
            return number_format($count);
        }

        if ($count < 1000000) {
            return number_format($count / 1000, 2) . 'K';
        }

        if ($count < 1000000000) {
            return number_format($count / 1000000, 3) . 'M';
        }

        return number_format($count / 1000000000, 3) . 'B';
    }
}
