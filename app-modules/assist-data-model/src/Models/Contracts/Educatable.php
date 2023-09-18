<?php

namespace Assist\AssistDataModel\Models\Contracts;

interface Educatable extends Identifiable
{
    public static function displayNameKey(): string;
}
