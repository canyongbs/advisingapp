<?php

namespace Assist\Authorization\View\Components;

use Closure;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Login extends Component
{
    public function render(): View|Closure|string
    {
        return view('components.login');
    }
}
