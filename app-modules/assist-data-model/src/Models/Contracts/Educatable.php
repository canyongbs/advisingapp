<?php

namespace Assist\AssistDataModel\Models\Contracts;

interface Educatable extends Identifiable
{
    public function displayName(): string;
}
