<?php

namespace Assist\ServiceManagement\Filament\Resources\ServiceRequestUpdateResource\Components;

use Filament\Actions\ViewAction;
use Assist\ServiceManagement\Filament\Concerns\ServiceRequestUpdateInfolist;

class ServiceRequestUpdateViewAction extends ViewAction
{
    use ServiceRequestUpdateInfolist;

    protected function setUp(): void
    {
        parent::setUp();

        $this->infolist($this->serviceRequestUpdateInfolist());
    }
}
