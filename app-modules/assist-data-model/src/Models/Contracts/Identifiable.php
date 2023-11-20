<?php

namespace Assist\AssistDataModel\Models\Contracts;

interface Identifiable
{
    public function identifier(): string;
}
