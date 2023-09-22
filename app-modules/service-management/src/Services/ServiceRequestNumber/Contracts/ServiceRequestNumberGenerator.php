<?php

namespace Assist\ServiceManagement\Services\ServiceRequestNumber\Contracts;

interface ServiceRequestNumberGenerator
{
    public function generate(): string;
}
