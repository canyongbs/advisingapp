<?php

namespace App\Livewire;

trait WithConfirmation
{
    public function confirm($callback, ...$argv)
    {
        $this->emit('confirm', compact('callback', 'argv'));
    }
}
